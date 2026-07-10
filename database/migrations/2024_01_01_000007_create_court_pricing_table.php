<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('court_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 10, 2);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('court_pricing', function (Blueprint $table) { $table->index('court_id'); });
    }
    public function down(): void { Schema::dropIfExists('court_pricing'); }
};