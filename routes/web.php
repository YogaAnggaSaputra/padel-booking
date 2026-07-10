<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'Padel Booking API',
        'version' => '1.0.0',
        'status' => 'online',
        'docs' => 'https://padel.ngodingyuk.site/api/v1',
        'endpoints' => [
            'health' => '/up',
            'auth' => '/api/login, /api/register, /api/me',
            'clubs' => '/api/clubs',
            'courts' => '/api/clubs/{club}/courts',
            'bookings' => '/api/clubs/{club}/bookings',
        ],
        'timestamp' => now()->toIso8601String(),
    ], 200);
});
