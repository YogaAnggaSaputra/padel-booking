<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('open');
            $table->unsignedTinyInteger('skill_level_min');
            $table->unsignedTinyInteger('skill_level_max');
            $table->unsignedInteger('max_players')->default(4);
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->boolean('is_competitive')->default(false);
            $table->text('description')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('matches', function (Blueprint $table) { $table->index(['club_id', 'court_id', 'start_time']); });
        Schema::create('matches', function (Blueprint $table) { $table->index('status'); });
    }
    public function down(): void { Schema::dropIfExists('matches'); }
};