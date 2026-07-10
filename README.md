# Padel Booking Platform

Laravel 11 + PostgreSQL + Redis. Multi-tenant padel court booking system.

## Stack
- Laravel 11 (PHP 8.2)
- PostgreSQL 16
- Redis 7
- Sanctum (API tokens)
- spatie/laravel-permission (RBAC)
- spatie/laravel-medialibrary
- simplesoftwareio/simple-qrcode
- barryvdh/laravel-dompdf
- Dummy payment gateway (swap to Midtrans via PAYMENT_GATEWAY env)

## Features (Fase 1 MVP)
- Multi-tenant (clubs)
- Court listing + dynamic pricing
- Booking with conflict detection
- Split payment via booking_participants
- Membership plans + credits
- Tournament/matchmaking (skeleton)
- Equipment rental
- Waitlist
- Notifications queue
- QR check-in

## Local dev
```bash
docker compose up -d
docker compose exec laravel.test bash
composer install
php artisan migrate --seed
```

## Deploy
Push to GitHub, Coolify auto-deploy via webhook.
Domain: https://padel.ngodingyuk.site
