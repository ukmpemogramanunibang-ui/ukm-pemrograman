<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('competition_participants')) {
            return;
        }

        Schema::create('competition_participants', function (Blueprint $table) {
            $table->id();

            // Kompetisi yang diikuti
            $table->foreignId('competition_id')
                ->constrained('competitions')
                ->cascadeOnDelete();

            // Siapa yang ikut (player game center / user)
            $table->foreignId('player_id')
                ->nullable()
                ->constrained('players')
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Bracket metadata
            $table->integer('seed')->nullable();            // seeding untuk bracket
            $table->integer('position')->nullable();        // posisi dalam bracket
            $table->string('bracket_side', 20)->nullable(); // 'left' | 'right' | 'center'

            // Status: active | eliminated | winner | withdrawn
            $table->string('status', 20)->default('active');

            // Snapshot nama (agar tetap ada meski player dihapus)
            $table->string('display_name')->nullable();

            // Statistik ringkas (untuk leaderboard)
            $table->integer('matches_played')->default(0);
            $table->integer('matches_won')->default(0);
            $table->integer('matches_lost')->default(0);
            $table->decimal('total_score', 15, 2)->default(0);

            $table->timestamps();

            // Index unik — 1 player/user per kompetisi
            $table->unique(['competition_id', 'player_id'], 'cp_comp_player_unique');
            $table->unique(['competition_id', 'user_id'],   'cp_comp_user_unique');

            // Index query umum
            $table->index(['competition_id', 'status'], 'cp_comp_status_idx');
            $table->index(['player_id'], 'cp_player_idx');
            $table->index(['user_id'], 'cp_user_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_participants');
    }
};