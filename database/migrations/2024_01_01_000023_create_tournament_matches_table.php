<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->integer('round');
            $table->integer('match_number');
            $table->foreignId('player1_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('player2_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('player1_score')->default(0);
            $table->integer('player2_score')->default(0);
            $table->date('match_date')->nullable();
            $table->time('match_time')->nullable();
            $table->foreignId('court_id')->nullable()->constrained('courts')->onDelete('set null');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'walkover', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->index(['tournament_id', 'round', 'match_number']);
        });
    }
    public function down(): void { Schema::dropIfExists('tournament_matches'); }
};
