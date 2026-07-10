<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\PaymentGateway;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function charge(Request $request, Booking $booking) {
        $validated = $request->validate(['method' => 'required|string']);
        $amount = $booking->final_amount;
        $result = app(PaymentGateway::class)->charge(['amount' => $amount, 'method' => $validated['method'], 'booking_id' => $booking->id]);
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'amount' => $amount,
            'method' => $validated['method'],
            'gateway' => 'dummy',
            'gateway_transaction_id' => $result['transaction_id'],
            'gateway_response' => $result,
            'status' => $result['status'] === 'paid' ? 'paid' : 'failed',
            'paid_at' => $result['paid_at'] ?? null,
        ]);
        $booking->update(['payment_status' => $payment->status]);
        return response()->json($payment, 201);
    }

    public function webhook(Request $request) {
        return response()->json(['received' => true]);
    }

    public function status(Request $request, Booking $booking) {
        return response()->json($booking->payments()->latest()->first());
    }
}
