<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('court_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
            $table->tinyInteger('day_of_week');
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['court_id', 'day_of_week']);
        });
    }
    public function down(): void { Schema::dropIfExists('court_availability'); }
};
