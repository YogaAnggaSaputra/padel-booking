<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('membership_plan_id')->constrained('membership_plans');
            $table->string('member_code', 30)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('remaining_credits')->default(0);
            $table->integer('remaining_sessions')->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled', 'frozen'])->default('active');
            $table->boolean('auto_renew')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('user_memberships'); }
};
