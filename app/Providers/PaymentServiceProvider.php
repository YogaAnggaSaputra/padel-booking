<?php
// app/Providers/PaymentServiceProvider.php
// ponytail: mark — single binding, swap by changing class string

namespace App\Providers;

use App\Services\Payment\DummyPaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\PaymentGateway::class,
            config('services.payment.gateway') ?? DummyPaymentGateway::class,
        );
    }
}
