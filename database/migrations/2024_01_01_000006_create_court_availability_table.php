<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('court_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();
            $table->string('exception_type')->default('blocked');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('reason')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('court_availability', function (Blueprint $table) { $table->index(['court_id', 'start_time', 'end_time']); });
    }
    public function down(): void { Schema::dropIfExists('court_availability'); }
};