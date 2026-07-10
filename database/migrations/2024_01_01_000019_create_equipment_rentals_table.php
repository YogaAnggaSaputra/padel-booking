<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_rentals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('reserved');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->boolean('deposit_returned')->default(false);
            $table->timestamp('pickup_at')->nullable();
            $table->timestamp('return_at')->nullable();
            $table->timestamp('due_at');
            $table->text('damage_report')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('equipment_rentals', function (Blueprint $table) { $table->index('booking_id'); });
        Schema::create('equipment_rentals', function (Blueprint $table) { $table->index('user_id'); });
    }
    public function down(): void { Schema::dropIfExists('equipment_rentals'); }
};