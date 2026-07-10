<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency')->default('IDR');
            $table->integer('duration_days');
            $table->integer('booking_credits')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->json('features')->default('[]');
            $table->integer('max_bookings_per_week')->nullable();
            $table->integer('priority_booking_hours')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
        });
        Schema::create('membership_plans', function (Blueprint $table) { $table->index('club_id'); });
    }
    public function down(): void { Schema::dropIfExists('membership_plans'); }
};