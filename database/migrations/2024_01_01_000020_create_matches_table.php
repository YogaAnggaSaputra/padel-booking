<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('court_id')->nullable()->constrained('courts')->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['friendly', 'ranked', 'tournament', 'open_match']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('match_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('skill_level_min', 2)->nullable();
            $table->string('skill_level_max', 2)->nullable();
            $table->enum('gender_preference', ['any', 'male', 'female', 'mixed'])->default('any');
            $table->integer('max_participants')->default(4);
            $table->decimal('cost_per_player', 10, 2)->default(0);
            $table->enum('status', ['open', 'full', 'cancelled', 'completed'])->default('open');
            $table->jsonb('rules')->default('{}');
            $table->timestamps();
            $table->index(['club_id', 'match_date', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('matches'); }
};
