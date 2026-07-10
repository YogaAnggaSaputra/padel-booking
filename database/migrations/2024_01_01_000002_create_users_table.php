<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('password_hash')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('avatar_path')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedTinyInteger('skill_level')->default(1);
            $table->string('preferred_hand')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('pending_verification');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) { $table->index('email'); });
        Schema::create('users', function (Blueprint $table) { $table->index('phone'); });
        Schema::create('users', function (Blueprint $table) { $table->index('status'); });
        Schema::create('users', function (Blueprint $table) { $table->index('skill_level'); });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};