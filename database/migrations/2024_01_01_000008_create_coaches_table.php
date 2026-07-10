<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->string('slug');
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('certification_level', 50)->nullable();
            $table->string('specialization')->nullable();
            $table->integer('years_experience')->default(0);
            $table->string('languages', 100)->nullable();
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('group_rate', 10, 2)->nullable();
            $table->jsonb('availability_pattern')->default('{}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
        });
    }
    public function down(): void { Schema::dropIfExists('coaches'); }
};
