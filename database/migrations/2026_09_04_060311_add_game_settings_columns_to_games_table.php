<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom pengaturan game ke tabel games
     */
    public function up(): void
    {
        // Cek apakah tabel games ada
        if (!Schema::hasTable('games')) {
            return;
        }

        Schema::table('games', function (Blueprint $table) {
            // ============================================================
            // CEK DAN TAMBAHKAN KOLOM SATU PER SATU (AMAN)
            // ============================================================

            // ---- INSTRUKSI & KATEGORI ----
            if (!Schema::hasColumn('games', 'instructions')) {
                $table->text('instructions')->nullable()->after('description')
                      ->comment('Instruksi permainan untuk pemain');
            }

            if (!Schema::hasColumn('games', 'category')) {
                $table->string('category')->nullable()->after('developer')
                      ->comment('Kategori game (Pemrograman, Matematika, dll)');
            }

            if (!Schema::hasColumn('games', 'game_mode')) {
                $table->string('game_mode')->default('practice')->after('category')
                      ->comment('Mode permainan: practice, battle, tournament, rank, vs');
            }

            // ---- SISTEM POIN ----
            if (!Schema::hasColumn('games', 'use_points')) {
                $table->boolean('use_points')->default(true)->after('game_mode')
                      ->comment('Apakah menggunakan sistem poin');
            }

            if (!Schema::hasColumn('games', 'point_correct')) {
                $table->integer('point_correct')->default(10)->after('use_points')
                      ->comment('Poin untuk jawaban benar');
            }

            if (!Schema::hasColumn('games', 'point_wrong')) {
                $table->integer('point_wrong')->default(0)->after('point_correct')
                      ->comment('Poin untuk jawaban salah (bisa negatif)');
            }

            if (!Schema::hasColumn('games', 'point_empty')) {
                $table->integer('point_empty')->default(0)->after('point_wrong')
                      ->comment('Poin untuk jawaban kosong/tidak dijawab');
            }

            if (!Schema::hasColumn('games', 'bonus_complete')) {
                $table->integer('bonus_complete')->default(20)->after('point_empty')
                      ->comment('Bonus poin saat menyelesaikan semua soal');
            }

            if (!Schema::hasColumn('games', 'bonus_streak')) {
                $table->integer('bonus_streak')->default(5)->after('bonus_complete')
                      ->comment('Bonus poin per streak (jawaban benar berurutan)');
            }

            if (!Schema::hasColumn('games', 'max_points')) {
                $table->integer('max_points')->default(1000)->after('bonus_streak')
                      ->comment('Batas maksimum poin per game');
            }

            // ---- DURASI & WAKTU ----
            if (!Schema::hasColumn('games', 'time_mode')) {
                $table->string('time_mode')->default('total')->after('max_points')
                      ->comment('Mode waktu: unlimited, total, per_question');
            }

            if (!Schema::hasColumn('games', 'time_total')) {
                $table->integer('time_total')->nullable()->after('time_mode')
                      ->comment('Durasi total game dalam menit (jika mode total)');
            }

            if (!Schema::hasColumn('games', 'time_per_question')) {
                $table->integer('time_per_question')->nullable()->after('time_total')
                      ->comment('Durasi per soal dalam detik (jika mode per_question)');
            }

            if (!Schema::hasColumn('games', 'timeout_action')) {
                $table->string('timeout_action')->default('skip')->after('time_per_question')
                      ->comment('Aksi saat waktu habis: skip, auto_next, finish');
            }

            // ---- ANTI-CHEAT & KEAMANAN ----
            if (!Schema::hasColumn('games', 'anti_cheat_detect_tab')) {
                $table->boolean('anti_cheat_detect_tab')->default(true)->after('timeout_action')
                      ->comment('Deteksi pindah tab');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_detect_blur')) {
                $table->boolean('anti_cheat_detect_blur')->default(true)->after('anti_cheat_detect_tab')
                      ->comment('Deteksi kehilangan fokus');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_detect_minimize')) {
                $table->boolean('anti_cheat_detect_minimize')->default(false)->after('anti_cheat_detect_blur')
                      ->comment('Deteksi minimize browser');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_detect_disconnect')) {
                $table->boolean('anti_cheat_detect_disconnect')->default(true)->after('anti_cheat_detect_minimize')
                      ->comment('Deteksi putus internet');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_random_question')) {
                $table->boolean('anti_cheat_random_question')->default(true)->after('anti_cheat_detect_disconnect')
                      ->comment('Randomisasi urutan soal');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_random_options')) {
                $table->boolean('anti_cheat_random_options')->default(true)->after('anti_cheat_random_question')
                      ->comment('Randomisasi pilihan jawaban');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_disable_copy_paste')) {
                $table->boolean('anti_cheat_disable_copy_paste')->default(true)->after('anti_cheat_random_options')
                      ->comment('Nonaktifkan copy-paste');
            }

            if (!Schema::hasColumn('games', 'anti_cheat_block_context_menu')) {
                $table->boolean('anti_cheat_block_context_menu')->default(true)->after('anti_cheat_disable_copy_paste')
                      ->comment('Blokir klik kanan');
            }

            if (!Schema::hasColumn('games', 'on_leave_action')) {
                $table->string('on_leave_action')->default('skip')->after('anti_cheat_block_context_menu')
                      ->comment('Aksi saat pemain keluar halaman: skip, pause, nothing');
            }

            if (!Schema::hasColumn('games', 'cheat_violation_limit')) {
                $table->integer('cheat_violation_limit')->default(3)->after('on_leave_action')
                      ->comment('Batas pelanggaran sebelum game over');
            }

            if (!Schema::hasColumn('games', 'cheat_penalty')) {
                $table->integer('cheat_penalty')->default(5)->after('cheat_violation_limit')
                      ->comment('Pengurangan poin per pelanggaran');
            }

            // ---- PENGATURAN MULTIPLAYER ----
            if (!Schema::hasColumn('games', 'max_players')) {
                $table->integer('max_players')->default(100)->after('cheat_penalty')
                      ->comment('Maksimum pemain dalam satu game');
            }

            if (!Schema::hasColumn('games', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('max_players')
                      ->comment('Apakah game bisa diakses publik');
            }

            if (!Schema::hasColumn('games', 'require_approval')) {
                $table->boolean('require_approval')->default(false)->after('is_public')
                      ->comment('Apakah perlu persetujuan untuk bergabung');
            }

            // ---- HASIL & STATISTIK ----
            if (!Schema::hasColumn('games', 'show_explanation')) {
                $table->string('show_explanation')->default('after_finish')->after('require_approval')
                      ->comment('Tampilkan pembahasan: after_each, after_finish, never');
            }

            if (!Schema::hasColumn('games', 'winner_criteria')) {
                $table->string('winner_criteria')->default('points')->after('show_explanation')
                      ->comment('Kriteria pemenang: points, accuracy, speed, combination');
            }

            if (!Schema::hasColumn('games', 'passing_score')) {
                $table->integer('passing_score')->default(60)->after('winner_criteria')
                      ->comment('Nilai minimal untuk lulus (0-100)');
            }

            // ---- SKILL (untuk quiz) ----
            if (!Schema::hasColumn('games', 'skill_enabled')) {
                $table->boolean('skill_enabled')->default(false)->after('passing_score')
                      ->comment('Apakah skill diaktifkan');
            }

            if (!Schema::hasColumn('games', 'skill_config')) {
                $table->json('skill_config')->nullable()->after('skill_enabled')
                      ->comment('Konfigurasi skill dalam JSON');
            }

            // ============================================================
            // TAMBAHKAN INDEX UNTUK PERFORMANCE
            // ============================================================
            $table->index('game_mode');
            $table->index('category');
            $table->index('is_public');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus semua kolom yang ditambahkan
     */
    public function down(): void
    {
        if (!Schema::hasTable('games')) {
            return;
        }

        Schema::table('games', function (Blueprint $table) {
            $columns = [
                'instructions',
                'category',
                'game_mode',
                'use_points',
                'point_correct',
                'point_wrong',
                'point_empty',
                'bonus_complete',
                'bonus_streak',
                'max_points',
                'time_mode',
                'time_total',
                'time_per_question',
                'timeout_action',
                'anti_cheat_detect_tab',
                'anti_cheat_detect_blur',
                'anti_cheat_detect_minimize',
                'anti_cheat_detect_disconnect',
                'anti_cheat_random_question',
                'anti_cheat_random_options',
                'anti_cheat_disable_copy_paste',
                'anti_cheat_block_context_menu',
                'on_leave_action',
                'cheat_violation_limit',
                'cheat_penalty',
                'max_players',
                'is_public',
                'require_approval',
                'show_explanation',
                'winner_criteria',
                'passing_score',
                'skill_enabled',
                'skill_config',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('games', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Hapus index
            $table->dropIndex(['game_mode']);
            $table->dropIndex(['category']);
            $table->dropIndex(['is_public']);
            $table->dropIndex(['status']);
        });
    }
};