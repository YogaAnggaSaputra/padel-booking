<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['unlimited', 'credits', 'sessions', 'period']);
            $table->integer('duration_days')->nullable();
            $table->integer('credits')->nullable();
            $table->integer('sessions_per_month')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->jsonb('benefits')->default('{}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
        });
    }
    public function down(): void { Schema::dropIfExists('membership_plans'); }
};
