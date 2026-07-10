<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\CoachController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\WaitlistController;
use App\Http\Controllers\Api\NotificationController;

// Public
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    // Clubs & courts
    Route::apiResource('clubs', ClubController::class);
    Route::get('clubs/{club}/courts', [CourtController::class, 'index']);
    Route::post('clubs/{club}/courts', [CourtController::class, 'store']);
    Route::get('clubs/{club}/courts/{court}', [CourtController::class, 'show']);
    Route::patch('clubs/{club}/courts/{court}', [CourtController::class, 'update']);
    Route::get('clubs/{club}/courts/{court}/availability', [CourtController::class, 'availability']);
    Route::get('clubs/{club}/courts/{court}/pricing', [CourtController::class, 'pricing']);

    // Bookings
    Route::get('clubs/{club}/bookings', [BookingController::class, 'index']);
    Route::post('clubs/{club}/bookings', [BookingController::class, 'store']);
    Route::get('clubs/{club}/bookings/{booking}', [BookingController::class, 'show']);
    Route::post('clubs/{club}/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::post('clubs/{club}/bookings/{booking}/check-in', [BookingController::class, 'checkIn']);

    // Payments
    Route::post('bookings/{booking}/pay', [PaymentController::class, 'charge']);
    Route::get('bookings/{booking}/payment', [PaymentController::class, 'status']);

    // Membership
    Route::get('clubs/{club}/membership-plans', [MembershipController::class, 'plans']);
    Route::post('clubs/{club}/membership-plans', [MembershipController::class, 'storePlan']);
    Route::post('clubs/{club}/membership-plans/{plan}/subscribe', [MembershipController::class, 'subscribe']);
    Route::get('me/memberships', [MembershipController::class, 'myMemberships']);

    // Coaches & lessons
    Route::get('clubs/{club}/coaches', [CoachController::class, 'index']);
    Route::post('clubs/{club}/coaches', [CoachController::class, 'store']);
    Route::get('clubs/{club}/coaches/{coach}', [CoachController::class, 'show']);
    Route::get('clubs/{club}/coaches/{coach}/lessons', [LessonController::class, 'index']);
    Route::post('clubs/{club}/coaches/{coach}/lessons', [LessonController::class, 'store']);
    Route::post('clubs/{club}/coaches/{coach}/lessons/{lesson}/enroll', [LessonController::class, 'enroll']);

    // Matchmaking
    Route::get('clubs/{club}/matches', [MatchController::class, 'index']);
    Route::post('clubs/{club}/matches', [MatchController::class, 'store']);
    Route::post('clubs/{club}/matches/{match}/join', [MatchController::class, 'join']);

    // Tournaments
    Route::get('clubs/{club}/tournaments', [TournamentController::class, 'index']);
    Route::post('clubs/{club}/tournaments', [TournamentController::class, 'store']);
    Route::get('clubs/{club}/tournaments/{tournament}', [TournamentController::class, 'show']);

    // Equipment
    Route::get('clubs/{club}/equipment', [EquipmentController::class, 'index']);
    Route::post('clubs/{club}/equipment', [EquipmentController::class, 'store']);
    Route::post('clubs/{club}/equipment/{equipment}/rent', [EquipmentController::class, 'rent']);

    // Waitlist
    Route::get('clubs/{club}/courts/{court}/waitlist', [WaitlistController::class, 'index']);
    Route::post('clubs/{club}/courts/{court}/waitlist', [WaitlistController::class, 'store']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
});
