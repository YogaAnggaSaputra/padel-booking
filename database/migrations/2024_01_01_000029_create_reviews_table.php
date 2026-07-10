<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->unique(['club_id', 'user_id', 'booking_id']);
        });
        Schema::create('reviews', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('reviews', function (Blueprint $table) { $table->index('coach_id'); });
    }
    public function down(): void { Schema::dropIfExists('reviews'); }
};