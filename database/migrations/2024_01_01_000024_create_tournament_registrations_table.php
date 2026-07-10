<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->unsignedInteger('seed_number')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->unique(['tournament_id', 'user_id']);
        });
        Schema::create('tournament_registrations', function (Blueprint $table) { $table->index('tournament_id'); });
    }
    public function down(): void { Schema::dropIfExists('tournament_registrations'); }
};