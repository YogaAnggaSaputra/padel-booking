<?php

return [
    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', \App\Services\Payment\DummyPaymentGateway::class),
    ],
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],
    'whatsapp' => [
        'provider' => env('WA_PROVIDER', 'dummy'),
    ],
];
