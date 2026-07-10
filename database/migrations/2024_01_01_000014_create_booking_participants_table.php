<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->enum('role', ['organizer', 'partner', 'opponent', 'spectator'])->default('partner');
            $table->decimal('share_amount', 10, 2)->default(0);
            $table->boolean('has_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index('booking_id');
        });
    }
    public function down(): void { Schema::dropIfExists('booking_participants'); }
};
