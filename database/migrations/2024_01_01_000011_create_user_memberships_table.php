<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained();
            $table->string('status')->default('pending');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->boolean('auto_renew')->default(false);
            $table->integer('credits_remaining')->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('user_memberships', function (Blueprint $table) { $table->index('user_id'); });
        Schema::create('user_memberships', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('user_memberships', function (Blueprint $table) { $table->index('status'); });
    }
    public function down(): void { Schema::dropIfExists('user_memberships'); }
};