<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('competition_matches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('competition_id')->constrained()->onDelete('cascade');
    $table->foreignId('round_id')->constrained('competition_rounds')->onDelete('cascade');
    $table->integer('match_number');
    $table->foreignId('participant_a_id')->nullable()->constrained('competition_participants')->onDelete('set null');
    $table->foreignId('participant_b_id')->nullable()->constrained('competition_participants')->onDelete('set null');
    $table->foreignId('winner_id')->nullable()->constrained('competition_participants')->onDelete('set null');
    $table->foreignId('loser_id')->nullable()->constrained('competition_participants')->onDelete('set null');
    $table->decimal('score_a', 10, 2)->default(0);
    $table->decimal('score_b', 10, 2)->default(0);
    $table->string('status')->default('upcoming'); // upcoming, active, completed, cancelled
    $table->foreignId('game_session_id')->nullable()->constrained('game_sessions')->onDelete('set null');
    $table->timestamp('started_at')->nullable();
    $table->timestamp('finished_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
