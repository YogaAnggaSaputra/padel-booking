<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('match_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['invited', 'joined', 'declined', 'left'])->default('joined');
            $table->boolean('has_paid')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['match_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('match_participants'); }
};
