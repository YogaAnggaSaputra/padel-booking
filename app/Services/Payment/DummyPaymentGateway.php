<?php
// app/Services/Payment/DummyPaymentGateway.php
// ponytail: mark — dummy, swap to Midtrans at Phase 2 with same interface

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Str;

class DummyPaymentGateway implements PaymentGateway
{
    /** Always succeeds unless amount < 0 */
    public function charge(array $params): array
    {
        if ($params['amount'] < 0) {
            return ['status' => 'failed', 'message' => 'Invalid amount'];
        }
        return [
            'status' => 'paid',
            'transaction_id' => 'DUMMY-' . strtoupper(Str::random(12)),
            'method' => $params['method'] ?? 'qris',
            'amount' => $params['amount'],
            'paid_at' => now()->toIso8601String(),
        ];
    }

    /** No-op for dummy */
    public function webhook(array $payload): array
    {
        return ['received' => true, 'transaction_id' => $payload['transaction_id'] ?? null];
    }
}
