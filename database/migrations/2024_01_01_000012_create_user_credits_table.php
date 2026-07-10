<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('type');
            $table->string('source');
            $table->decimal('balance_after', 12, 2);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->text('notes')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamp('created_at');
        });
        Schema::create('user_credits', function (Blueprint $table) { $table->index(['user_id', 'club_id']); });
    }
    public function down(): void { Schema::dropIfExists('user_credits'); }
};