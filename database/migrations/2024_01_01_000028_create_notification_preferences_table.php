<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel');
            $table->string('event_type');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'club_id', 'channel', 'event_type']);
        });
    }
    public function down(): void { Schema::dropIfExists('notification_preferences'); }
};