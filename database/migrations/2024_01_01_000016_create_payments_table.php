<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('booking_participants')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('IDR');
            $table->string('method');
            $table->string('gateway');
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table) { $table->index('booking_id'); });
        Schema::create('payments', function (Blueprint $table) { $table->index('user_id'); });
        Schema::create('payments', function (Blueprint $table) { $table->index('gateway_transaction_id'); });
    }
    public function down(): void { Schema::dropIfExists('payments'); }
};