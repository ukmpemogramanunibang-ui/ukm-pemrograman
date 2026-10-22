<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('game_matches', function (Blueprint $table) {
        $table->id();
        $table->foreignId('game_id')->constrained()->onDelete('cascade');
        $table->foreignId('player1_id')->constrained('players')->onDelete('cascade');
        $table->foreignId('player2_id')->nullable()->constrained('players')->onDelete('cascade');
        $table->enum('status', ['waiting', 'active', 'finished', 'cancelled', 'disconnected'])->default('waiting');
        $table->foreignId('winner_id')->nullable()->constrained('players')->onDelete('set null');
        $table->integer('score_player1')->nullable();
        $table->integer('score_player2')->nullable();
        $table->json('match_data')->nullable();
        $table->timestamp('started_at')->nullable();
        $table->timestamp('ended_at')->nullable();
        $table->timestamps();
        $table->index(['game_id', 'status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_matches');
    }
};
