<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained()->nullOnDelete();
            $table->string('booking_type')->default('regular');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->unsignedInteger('number_of_players')->default(4);
            $table->json('players')->default('[]');
            $table->boolean('is_recurring')->default(false);
            $table->uuid('recurring_group_uuid')->nullable();
            $table->json('recurring_rule')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2);
            $table->string('currency')->default('IDR');
            $table->text('notes')->nullable();
            $table->string('source')->default('app');
            $table->string('qr_code_path')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('bookings', function (Blueprint $table) { $table->index(['club_id', 'court_id', 'start_time', 'end_time']); });
        Schema::create('bookings', function (Blueprint $table) { $table->index('user_id'); });
        Schema::create('bookings', function (Blueprint $table) { $table->index('status'); });
        Schema::create('bookings', function (Blueprint $table) { $table->index('start_time'); });
        Schema::create('bookings', function (Blueprint $table) { $table->index('recurring_group_uuid')->where('is_recurring', true); });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};