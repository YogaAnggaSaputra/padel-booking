<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['racket', 'ball', 'shoe', 'grip', 'wristband', 'other']);
            $table->text('description')->nullable();
            $table->integer('total_stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->decimal('rental_price', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['club_id', 'type', 'is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('equipment'); }
};
