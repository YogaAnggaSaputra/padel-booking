<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        DB::statement("CREATE MATERIALIZED VIEW court_utilization_daily AS SELECT c.club_id, c.id AS court_id, DATE(b.start_time) AS date, COUNT(b.id) AS booking_count, SUM(b.final_amount) AS revenue, SUM(EXTRACT(EPOCH FROM (b.end_time - b.start_time)) / 3600) AS hours_booked, COUNT(DISTINCT b.user_id) AS unique_players FROM bookings b JOIN courts c ON b.court_id = c.id WHERE b.status NOT IN ('cancelled', 'no_show') GROUP BY c.club_id, c.id, DATE(b.start_time)");
        DB::statement('CREATE UNIQUE INDEX idx_court_utilization_daily ON court_utilization_daily (club_id, court_id, date)');
    }
    public function down(): void {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS court_utilization_daily');
    }
};