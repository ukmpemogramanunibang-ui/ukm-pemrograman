<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('decision_wheel_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained('decision_wheels')->cascadeOnDelete();
            $table->foreignId('operator_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('spin_number');
            $table->unsignedSmallInteger('duration');
            $table->foreignId('winner_id')->nullable()->constrained('decision_wheel_entries')->nullOnDelete();
            $table->json('winner_ids')->nullable();
            $table->decimal('start_angle', 10, 4)->default(0);
            $table->decimal('target_angle', 10, 4)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['wheel_id', 'spin_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_wheel_spins');
    }
};