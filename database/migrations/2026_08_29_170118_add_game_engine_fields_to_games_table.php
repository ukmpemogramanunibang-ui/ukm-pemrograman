<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // ============================================================
            // QUIZ SETTINGS
            // ============================================================
            if (!Schema::hasColumn('games', 'quiz_settings')) {
                $table->json('quiz_settings')->nullable()->after('game_settings');
            }

            // ============================================================
            // PROCTORING SETTINGS (untuk quiz & typing)
            // ============================================================
            if (!Schema::hasColumn('games', 'proctoring_settings')) {
                $table->json('proctoring_settings')->nullable()->after('quiz_settings');
            }

            // ============================================================
            // TYPING ENGINE SETTINGS
            // ============================================================
            if (!Schema::hasColumn('games', 'typing_settings')) {
                $table->json('typing_settings')->nullable()->after('proctoring_settings');
            }

            // ============================================================
            // GAME IMAGES (max 3)
            // ============================================================
            if (!Schema::hasColumn('games', 'game_images')) {
                $table->json('game_images')->nullable()->after('typing_settings');
            }

            // ============================================================
            // QUESTIONS (untuk quiz & tebak)
            // ============================================================
            if (!Schema::hasColumn('games', 'quiz_questions')) {
                $table->json('quiz_questions')->nullable()->after('game_images');
            }

            // ============================================================
            // MAX ATTEMPTS
            // ============================================================
            if (!Schema::hasColumn('games', 'max_attempts')) {
                $table->integer('max_attempts')->default(1)->after('quiz_questions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'quiz_settings',
                'proctoring_settings',
                'typing_settings',
                'game_images',
                'quiz_questions',
                'max_attempts'
            ]);
        });
    }
};