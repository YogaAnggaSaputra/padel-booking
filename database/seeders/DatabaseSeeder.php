<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Club;
use App\Models\Court;
use App\Models\MembershipPlan;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'email' => 'admin@padel.local',
            'phone' => '+6281234567890',
            'first_name' => 'Admin',
            'last_name' => 'Demo',
            'password_hash' => Hash::make('password'),
            'skill_level' => 5,
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        $player = User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'email' => 'player@padel.local',
            'first_name' => 'Demo',
            'last_name' => 'Player',
            'password_hash' => Hash::make('password'),
            'skill_level' => 3,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $club = Club::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Jakarta Padel Club',
            'slug' => 'jakarta-padel',
            'description' => 'Premier padel club in central Jakarta',
            'address' => 'Jl. Sudirman No.1',
            'city' => 'Jakarta',
            'phone' => '+6281234567890',
            'email' => 'hello@jakarta-padel.id',
        ]);

        ClubMember::create(['club_id' => $club->id, 'user_id' => $owner->id, 'role' => 'owner', 'joined_at' => now()]);
        ClubMember::create(['club_id' => $club->id, 'user_id' => $player->id, 'role' => 'member', 'joined_at' => now()]);

        Court::create([
            'club_id' => $club->id, 'name' => 'Court 1', 'slug' => 'court-1',
            'court_type' => 'outdoor', 'surface_type' => 'artificial_grass',
            'base_price' => 150000, 'peak_price' => 250000, 'off_peak_price' => 100000,
        ]);
        Court::create([
            'club_id' => $club->id, 'name' => 'Court 2', 'slug' => 'court-2',
            'court_type' => 'indoor', 'surface_type' => 'crystal',
            'base_price' => 200000, 'peak_price' => 300000, 'off_peak_price' => 150000,
        ]);

        MembershipPlan::create([
            'club_id' => $club->id, 'name' => 'Monthly Pass', 'slug' => 'monthly',
            'description' => 'Unlimited bookings Mon-Fri off-peak',
            'price' => 1500000, 'duration_days' => 30, 'booking_credits' => 0,
            'features' => json_encode(['Unlimited Mon-Fri off-peak', '5% discount weekends']),
        ]);
    }
}
