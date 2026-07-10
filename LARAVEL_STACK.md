# Padel Booking Platform — Laravel Stack

## Decision (Ponytail-climbed)

After `ponytail:decision-ladder`:

1. **Need all features?** NO — Fase 1 MVP only. The rest is "planned". 
2. **Stdlib?** Laravel migrations + Eloquent. 
3. **Native?** UUID, soft-deletes, eager-loading, scopes. 
4. **Dep already installed?** NONE — greenfield, but we have internet, so `composer require` is on the table.
5. **One line?** Each migration = one `Schema::create()` call. Each model = one class.

## Pick (NOT both, NOT all — minimum)

| Concern | Pick | Why | Reject |
|---------|------|-----|--------|
| Tenancy | `club_id` FK + global scope | Single DB, cheap, works for 99% of multi-tenant apps | stancl/tenancy = overkill for MVP, requires separate DB per tenant |
| Auth | Laravel Sanctum + Socialite | First-party | Passport = OAuth2 server, not needed for player login |
| RBAC | spatie/laravel-permission | De-facto standard | Custom roles = maintenance hell |
| Media | spatie/laravel-medialibrary | First-party storage | Custom = reinventing wheel |
| Query | spatie/laravel-query-builder | whitelisted filters | Custom = security risk |
| Payment | midtrans/midtrans-php | Snap API + split-payment docs consistent | xendit (also fine, but Midtrans was user's pick) |
| WhatsApp | `venom/bot` (Node bridge) | Most stable WA unofficial | Meta WA API = expensive approval |
| QR | simplesoftwareio/simple-qrcode | One-liner | Custom = waste |
| PDF | barryvdh/laravel-dompdf | Stable | snappy/wkhtmltopdf = needs binary |

## File Layout (Ponytail: fewest files)

```
app/
├── Models/
│   ├── Club.php              (1 file)
│   ├── User.php              (1 file)
│   ├── Court.php             (1 file)
│   ├── Booking.php           (1 file)
│   ├── BookingParticipant.php
│   ├── Payment.php
│   ├── MembershipPlan.php
│   ├── UserMembership.php
│   ├── UserCredit.php
│   ├── Coach.php
│   ├── Lesson.php
│   ├── Match.php
│   ├── Tournament.php
│   ├── Equipment.php
│   ├── EquipmentRental.php
│   ├── WaitlistEntry.php
│   ├── Notification.php
│   ├── Review.php
│   ├── ClubMember.php
│   └── EquipmentRental.php
├── Http/Controllers/Api/
│   ├── AuthController.php
│   ├── ClubController.php
│   ├── CourtController.php
│   ├── BookingController.php
│   ├── PaymentController.php
│   ├── MembershipController.php
│   ├── CoachController.php
│   ├── LessonController.php
│   ├── MatchController.php
│   ├── TournamentController.php
│   ├── EquipmentController.php
│   ├── WaitlistController.php
│   └── NotificationController.php
├── Http/Requests/    (Form Requests per controller, 1 file each)
├── Http/Resources/   (JSON Resources per model, 1 file each)
└── Observers/        (booking event, payment event, etc — 1 file each)
database/migrations/  (33 migrations, 1 per table, no extras)
database/seeders/     (1 ClubSeeder + 1 DemoDataSeeder for testing)
routes/api.php        (single file, resource routes)
```

## Routes (1 file, resource-only)

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clubs', ClubController::class);
    Route::apiResource('clubs.courts', CourtController::class)->shallow();
    Route::apiResource('clubs.bookings', BookingController::class)->shallow();
    Route::apiResource('bookings.participants', BookingParticipantController::class)->shallow();
    Route::apiResource('clubs.membership-plans', MembershipPlanController::class)->shallow();
    Route::apiResource('memberships', UserMembershipController::class);
    Route::apiResource('clubs.coaches', CoachController::class)->shallow();
    Route::apiResource('coaches.lessons', LessonController::class)->shallow();
    Route::apiResource('clubs.matches', MatchController::class)->shallow();
    Route::apiResource('clubs.tournaments', TournamentController::class)->shallow();
    Route::apiResource('clubs.equipment', EquipmentController::class)->shallow();
    Route::apiResource('clubs.waitlist', WaitlistController::class)->shallow();
    Route::apiResource('notifications', NotificationController::class);
    Route::post('bookings/{booking}/check-in', [BookingController::class, 'checkIn']);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::post('payments/{payment}/webhook/midtrans', [PaymentController::class, 'webhook']);
    Route::get('reports/court-utilization', [ReportController::class, 'courtUtilization']);
});
```

## What gets dropped (Ponytail: deletion over addition)

- ❌ `stancl/tenancy` — multi-tenant via club_id FK is enough
- ❌ `spatie/laravel-activitylog` — audit_logs table is enough, no UI
- ❌ `laravel/socialite` for now — Fase 1 uses email+phone only
- ❌ `laravel/scout` — no search MVP, raw LIKE is fine
- ❌ `predis/predis` — PHP redis client built-in
- ❌ `intervention/image` — spatie/medialibrary handles thumbs
- ❌ `barryvdh/laravel-debugbar` — dev only, not in prod
- ❌ `spatie/laravel-sluggable` — slug generated in migration, no need
- ❌ `laravel/horizon` — Fase 1 no queue UI
- ❌ `laravel/sanctum` for SPA — token only, no SPA needed

## What gets kept (must)

- `laravel/sanctum` — API token auth
- `spatie/laravel-permission` — RBAC
- `spatie/laravel-medialibrary` — court/equipment photos
- `spatie/laravel-query-builder` — whitelisted filters
- `simplesoftwareio/simple-qrcode` — booking QR
- `barryvdh/laravel-dompdf` — invoice PDF
- `midtrans/midtrans-php` — payment

## What gets kept (Phase 2+)

- `whatsapp/venom` bridge (when Fase 2 starts)
- `mohammadian/laravel-tournaments` only if homebrew is too much

## ponytail:mark — single-tenant upgrade path

If the platform outgrows single-DB-per-tenant-per-club, add `club_id` to `cache:` prefix
(already scoped) and Redis key. **No schema change needed.**

If payments volume > 1k tx/day, swap `midtrans/midtrans-php` to webhook-only with
queue worker. **No schema change needed.**
