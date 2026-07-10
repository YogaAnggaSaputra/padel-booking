<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('channel');
            $table->string('status')->default('pending');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->json('payload')->default('{}');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('notifications', function (Blueprint $table) { $table->index('user_id'); });
        Schema::create('notifications', function (Blueprint $table) { $table->index('status'); });
        Schema::create('notifications', function (Blueprint $table) { $table->index('scheduled_at')->where('status', 'pending'); });
    }
    public function down(): void { Schema::dropIfExists('notifications'); }
};