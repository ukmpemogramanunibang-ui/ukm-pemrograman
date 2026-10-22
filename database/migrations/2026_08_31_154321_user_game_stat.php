<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_game_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->integer('total_plays')->default(0);
            $table->integer('total_score')->default(0);
            $table->integer('best_score')->default(0);
            $table->decimal('avg_score', 8, 2)->default(0);
            $table->json('last_metrics')->nullable();
            $table->timestamp('last_played_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'game_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_game_stats');
    }
};