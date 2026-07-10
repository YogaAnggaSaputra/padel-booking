<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('poster_path')->nullable();
            $table->date('registration_start')->nullable();
            $table->date('registration_end')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('format', ['single_elimination', 'double_elimination', 'round_robin', 'swiss']);
            $table->enum('category', ['mens_singles', 'womens_singles', 'mens_doubles', 'womens_doubles', 'mixed_doubles']);
            $table->integer('max_participants');
            $table->decimal('entry_fee', 10, 2);
            $table->decimal('prize_pool', 10, 2)->default(0);
            $table->jsonb('prize_distribution')->default('{}');
            $table->enum('status', ['draft', 'open_registration', 'closed', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->jsonb('rules')->default('{}');
            $table->timestamps();
            $table->unique(['club_id', 'slug']);
            $table->index(['status', 'start_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('tournaments'); }
};
