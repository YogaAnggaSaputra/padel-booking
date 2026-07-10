<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->string('currency', 3)->default('IDR');
            $table->enum('method', ['midtrans', 'xendit', 'cash', 'card', 'ewallet', 'va', 'qris']);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded', 'expired', 'cancelled'])->default('pending');
            $table->string('external_id')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('va_number')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->jsonb('gateway_response')->default('{}');
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index('external_id');
        });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};
