<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->onDelete('set null');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes');
            $table->decimal('price_per_hour', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'partial', 'refunded'])->default('unpaid');
            $table->string('qr_code_path')->nullable();
            $table->string('recurring_group_uuid')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->index(['court_id', 'booking_date', 'start_time'], 'idx_court_date_time');
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index('recurring_group_uuid');
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
