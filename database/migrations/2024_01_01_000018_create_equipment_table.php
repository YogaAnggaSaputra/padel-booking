<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price_per_hour', 10, 2);
            $table->decimal('price_per_day', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->unsignedInteger('quantity_total');
            $table->unsignedInteger('quantity_available');
            $table->string('condition_status')->default('good');
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();
        });
        Schema::create('equipment', function (Blueprint $table) { $table->index('club_id'); });
        Schema::create('equipment', function (Blueprint $table) { $table->index('is_active'); });
    }
    public function down(): void { Schema::dropIfExists('equipment'); }
};