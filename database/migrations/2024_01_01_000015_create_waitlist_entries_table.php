<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->timestamp('desired_start_time');
            $table->timestamp('desired_end_time');
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('number_of_players')->default(4);
            $table->string('status')->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->foreignId('converted_booking_id')->nullable()->constrained('bookings');
            $table->timestamp('expires_at');
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('waitlist_entries', function (Blueprint $table) { $table->index(['court_id', 'desired_start_time']); });
        Schema::create('waitlist_entries', function (Blueprint $table) { $table->index('status'); });
    }
    public function down(): void { Schema::dropIfExists('waitlist_entries'); }
};