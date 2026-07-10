<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('requested_date');
            $table->time('requested_start');
            $table->time('requested_end');
            $table->integer('priority')->default(0);
            $table->enum('status', ['waiting', 'notified', 'converted', 'expired'])->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['court_id', 'requested_date', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('waitlist_entries'); }
};
