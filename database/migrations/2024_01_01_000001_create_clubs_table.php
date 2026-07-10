<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('country')->default('ID');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->time('opening_time')->default('06:00');
            $table->time('closing_time')->default('23:00');
            $table->integer('advance_booking_days')->default(14);
            $table->integer('cancellation_hours')->default(24);
            $table->boolean('auto_confirm_booking')->default(false);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->index('city');
            $table->index('is_active');
        });
    }
    public function down(): void { Schema::dropIfExists('clubs'); }
};
