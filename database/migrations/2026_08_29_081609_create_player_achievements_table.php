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
    Schema::create('player_achievements', function (Blueprint $table) {
        $table->foreignId('player_id')->constrained()->onDelete('cascade');
        $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
        $table->timestamp('earned_at')->useCurrent();
        $table->primary(['player_id', 'achievement_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_achievements');
    }
};
