<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: cegah error jika tabel sudah dibuat oleh migration lain
        if (Schema::hasTable('competition_matches')) {
            return;
        }

        Schema::create('competition_matches', function (Blueprint $table) {
            $table->id();

            // Relasi kompetisi — tabel ini PASTI ada
            $table->foreignId('competition_id')
                ->constrained('competitions')
                ->cascadeOnDelete();

            $table->foreignId('round_id')
                ->constrained('competition_rounds')
                ->cascadeOnDelete();

            $table->integer('match_number');

            // ⚠️ Peserta — tabel competition_participants TIDAK ADA migration-nya
            // Pakai kolom biasa tanpa FK constraint agar migrasi tidak gagal.
            // Kalau nanti tabel competition_participants dibuat, tinggal tambah migration untuk FK-nya.
            $table->unsignedBigInteger('participant_a_id')->nullable();
            $table->unsignedBigInteger('participant_b_id')->nullable();
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->unsignedBigInteger('loser_id')->nullable();

            $table->decimal('score_a', 10, 2)->default(0);
            $table->decimal('score_b', 10, 2)->default(0);

            $table->string('status', 20)->default('upcoming');

            // Relasi ke game session — tabel game_sessions SUDAH ADA (dibuat 2026_08_29_081557)
            $table->foreignId('game_session_id')
                ->nullable()
                ->constrained('game_sessions')
                ->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            // Index untuk query umum
            $table->index(['competition_id', 'status'], 'cm_comp_status_idx');
            $table->index(['round_id', 'match_number'], 'cm_round_num_idx');
            $table->index('participant_a_id', 'cm_part_a_idx');
            $table->index('participant_b_id', 'cm_part_b_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_matches');
    }
};