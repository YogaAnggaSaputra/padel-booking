<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('lesson_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['enrolled', 'attended', 'absent', 'cancelled'])->default('enrolled');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->boolean('has_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['lesson_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('lesson_enrollments'); }
};
