<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('court_type')->default('outdoor');
            $table->string('surface_type')->default('artificial_grass');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->decimal('base_price', 10, 2);
            $table->decimal('peak_price', 10, 2)->nullable();
            $table->decimal('off_peak_price', 10, 2)->nullable();
            $table->integer('booking_slot_duration')->default(60);
            $table->integer('max_players')->default(4);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('order_index')->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
        });
        Schema::create('courts', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('courts', function (Blueprint $table) { $table->index('is_available'); });
    }
    public function down(): void { Schema::dropIfExists('courts'); }
};