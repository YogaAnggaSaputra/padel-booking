<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable()->unique();
            $table->string('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('skill_level', 2)->nullable();
            $table->integer('total_matches')->default(0);
            $table->decimal('rating', 4, 2)->default(0);
            $table->jsonb('preferences')->default('{}');
            $table->rememberToken();
            $table->timestamps();
            $table->index('skill_level');
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
