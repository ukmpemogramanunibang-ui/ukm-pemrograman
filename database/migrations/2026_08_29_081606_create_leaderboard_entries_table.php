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
    Schema::create('leaderboard_entries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('leaderboard_id')->constrained()->onDelete('cascade');
        $table->foreignId('player_id')->constrained()->onDelete('cascade');
        $table->integer('rank');
        $table->integer('score');
        $table->timestamps();
        $table->unique(['leaderboard_id', 'player_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_entries');
    }
};
