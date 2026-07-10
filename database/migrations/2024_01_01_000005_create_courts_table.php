<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->enum('type', ['indoor', 'outdoor', 'covered', 'panoramic']);
            $table->enum('surface', ['glass', 'wall', 'artificial_grass', 'concrete']);
            $table->boolean('has_lighting')->default(true);
            $table->boolean('has_heating')->default(false);
            $table->integer('capacity_players')->default(4);
            $table->jsonb('amenities')->default('{}');
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
            $table->index(['club_id', 'is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('courts'); }
};
