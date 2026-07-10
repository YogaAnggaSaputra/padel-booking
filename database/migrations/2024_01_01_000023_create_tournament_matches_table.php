<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('round');
            $table->unsignedInteger('match_number');
            $table->string('bracket_position')->nullable();
            $table->foreignId('player1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('player2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('player1_score')->nullable();
            $table->json('player2_score')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->unique(['tournament_id', 'round', 'match_number']);
        });
        Schema::create('tournament_matches', function (Blueprint $table) { $table->index('tournament_id'); });
    }
    public function down(): void { Schema::dropIfExists('tournament_matches'); }
};