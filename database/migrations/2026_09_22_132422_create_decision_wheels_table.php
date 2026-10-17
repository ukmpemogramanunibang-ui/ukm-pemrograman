<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('decision_wheels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_token', 16)->unique();
            $table->string('controller_token_hash')->nullable();
            $table->string('title');
            $table->enum('selection_mode', ['weighted', 'secret_random'])->default('weighted');
            $table->enum('result_mode', ['single', 'multi', 'ranking'])->default('single');
            $table->unsignedTinyInteger('winner_count')->default(1);
            $table->enum('duration', [10, 30, 60, 120, 150])->default(60);
            $table->enum('sound_mode', ['system', 'upload', 'none'])->default('system');
            $table->string('sound_path')->nullable();
            $table->enum('status', ['draft', 'ready', 'spinning', 'finished', 'paused', 'archived'])->default('ready');
            $table->string('operator_session_id')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();

            $table->index(['public_token']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_wheels');
    }
};