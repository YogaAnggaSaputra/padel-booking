<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->text('bio')->nullable();
            $table->json('certifications')->default('[]');
            $table->json('specialties')->default('[]');
            $table->boolean('is_active')->default(true);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->unique(['user_id', 'club_id']);
        });
        Schema::create('coaches', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('coaches', function (Blueprint $table) { $table->index('user_id'); });
    }
    public function down(): void { Schema::dropIfExists('coaches'); }
};