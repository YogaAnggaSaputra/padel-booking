<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('court_id')->nullable()->constrained('courts')->onDelete('set null');
            $table->foreignId('coach_id')->constrained('coaches')->onDelete('restrict');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['private', 'group', 'kids', 'advanced', 'beginner']);
            $table->integer('max_students')->default(1);
            $table->integer('duration_minutes')->default(60);
            $table->decimal('price', 10, 2);
            $table->date('lesson_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->string('skill_level', 2)->nullable();
            $table->jsonb('curriculum')->default('{}');
            $table->timestamps();
            $table->index(['club_id', 'lesson_date', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('lessons'); }
};
