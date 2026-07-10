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
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request, Club $club) {
        $q = Booking::where('club_id', $club->id)->with('court', 'user', 'participants');
        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('date')) $q->whereDate('start_time', $request->date);
        if ($request->filled('court_id')) $q->where('court_id', $request->court_id);
        return response()->json($q->paginate(20));
    }

    public function store(Request $request, Club $club) {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'number_of_players' => 'required|integer|min:1|max:4',
            'players' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);
        $validated['club_id'] = $club->id;
        $validated['user_id'] = Auth::id();
        $validated['uuid'] = (string) Str::uuid();
        $validated['booking_type'] = 'regular';
        $validated['status'] = 'pending';
        $validated['payment_status'] = 'pending';
        $validated['total_amount'] = 0;
        $validated['final_amount'] = 0;
        $validated['source'] = 'app';
        $booking = Booking::create($validated);
        BookingParticipant::create(['booking_id' => $booking->id, 'user_id' => Auth::id(), 'is_organizer' => true, 'joined_at' => now()]);
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
