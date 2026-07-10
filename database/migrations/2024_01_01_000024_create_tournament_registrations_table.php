<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tournament_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('partner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'confirmed', 'waitlisted', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('entry_fee_paid', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->integer('seed_number')->nullable();
            $table->timestamps();
            $table->unique(['tournament_id', 'user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('tournament_registrations'); }
};
