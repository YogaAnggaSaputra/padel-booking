<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coach_id')->constrained()->cascadeOnDelete();
            $table->foreignId('court_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lesson_type')->default('private');
            $table->string('status')->default('scheduled');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->unsignedInteger('max_students')->default(1);
            $table->unsignedInteger('current_students')->default(0);
            $table->decimal('price_per_student', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->unsignedTinyInteger('skill_level_min')->nullable();
            $table->unsignedTinyInteger('skill_level_max')->nullable();
            $table->text('notes')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('lessons', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('lessons', function (Blueprint $table) { $table->index('coach_id'); });
        Schema::create('lessons', function (Blueprint $table) { $table->index('status'); });
    }
    public function down(): void { Schema::dropIfExists('lessons'); }
};