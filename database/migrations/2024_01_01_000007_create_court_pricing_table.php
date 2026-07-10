<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('court_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
            $table->enum('period_type', ['peak', 'off_peak', 'weekend', 'holiday', 'morning', 'evening']);
            $table->string('name');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('price_per_hour', 10, 2);
            $table->decimal('weekend_price_per_hour', 10, 2)->nullable();
            $table->jsonb('days_of_week')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['court_id', 'is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('court_pricing'); }
};
