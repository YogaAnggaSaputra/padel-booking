<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->boolean('is_organizer')->default(false);
            $table->string('payment_status')->default('pending');
            $table->decimal('share_amount', 10, 2)->default(0);
            $table->timestamp('joined_at');
            $table->jsonb('metadata')->default('{}');
        });
        Schema::create('booking_participants', function (Blueprint $table) { $table->index('booking_id'); });
        Schema::create('booking_participants', function (Blueprint $table) { $table->index('user_id'); });
    }
    public function down(): void { Schema::dropIfExists('booking_participants'); }
};