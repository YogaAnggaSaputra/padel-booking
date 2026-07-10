<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('format');
            $table->string('status')->default('draft');
            $table->unsignedInteger('max_participants')->nullable();
            $table->unsignedTinyInteger('min_skill_level')->nullable();
            $table->unsignedTinyInteger('max_skill_level')->nullable();
            $table->timestamp('registration_start');
            $table->timestamp('registration_end');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('entry_fee', 10, 2)->default(0);
            $table->text('prize_description')->nullable();
            $table->json('rules')->default('[]');
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
        });
        Schema::create('tournaments', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('tournaments', function (Blueprint $table) { $table->index('status'); });
    }
    public function down(): void { Schema::dropIfExists('tournaments'); }
};