<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Club;
use App\Models\Court;
use App\Models\BookingParticipant;
use App\Contracts\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request, Club $club) {
        $q = Booking::where('club_id', $club->id)->with('court', 'user', 'participants');
        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('date')) $q->whereDate('booking_date', $request->date);
        if ($request->filled('court_id')) $q->where('court_id', $request->court_id);
        return response()->json($q->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'required|integer|min:30',
            'price_per_hour' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        $validated['club_id'] = $club->id;
        $validated['user_id'] = Auth::id();
        $validated['uuid'] = (string) Str::uuid();
        $validated['status'] = 'pending';
        $validated['payment_status'] = 'unpaid';
        $booking = Booking::create($validated);
        BookingParticipant::create(['booking_id' => $booking->id, 'user_id' => Auth::id(), 'role' => 'organizer', 'joined_at' => now()]);
        return response()->json($booking->load('court', 'participants'), 201);
    }

    public function show(Club $club, Booking $booking) {
        return response()->json($booking->load('court', 'user', 'participants', 'payments', 'equipmentRentals'));
    }

    public function cancel(Request $request, Club $club, Booking $booking) {
        $request->validate(['reason' => 'nullable|string']);
        $booking->update(['status' => 'cancelled', 'cancelled_by' => Auth::id(), 'cancelled_at' => now(), 'cancellation_reason' => $request->reason]);
        return response()->json(['message' => 'Booking cancelled']);
    }

    public function checkIn(Request $request, Club $club, Booking $booking) {
        $booking->update(['status' => 'checked_in', 'checked_in_at' => now()]);
        return response()->json(['message' => 'Checked in', 'qr_code' => $booking->qr_code_path]);
    }
}
