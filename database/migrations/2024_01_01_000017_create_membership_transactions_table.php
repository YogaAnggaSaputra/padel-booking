<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('membership_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_membership_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('IDR');
            $table->string('status')->default('pending');
            $table->string('gateway_transaction_id')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('membership_transactions'); }
};