<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('match_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('skill_level');
            $table->boolean('is_confirmed')->default(false);
            $table->timestamp('joined_at');
            $table->unique(['match_id', 'user_id']);
        });
        Schema::create('match_participants', function (Blueprint $table) { $table->index('match_id'); });
    }
    public function down(): void { Schema::dropIfExists('match_participants'); }
};