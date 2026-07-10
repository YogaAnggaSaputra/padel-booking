<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->integer('quantity')->default(1);
            $table->decimal('rental_price', 10, 2);
            $table->decimal('deposit_paid', 10, 2)->default(0);
            $table->boolean('returned')->default(false);
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
            $table->index(['booking_id', 'returned']);
        });
    }
    public function down(): void { Schema::dropIfExists('equipment_rentals'); }
};
