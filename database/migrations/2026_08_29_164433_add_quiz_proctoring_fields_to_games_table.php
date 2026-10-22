<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Quiz specific settings
            if (!Schema::hasColumn('games', 'quiz_settings')) {
                $table->json('quiz_settings')->nullable()->after('game_settings');
            }

            // Proctoring settings
            if (!Schema::hasColumn('games', 'proctoring_settings')) {
                $table->json('proctoring_settings')->nullable()->after('quiz_settings');
            }

            // Max attempts (0 = unlimited, 1 = once, etc.)
            if (!Schema::hasColumn('games', 'max_attempts')) {
                $table->integer('max_attempts')->default(1)->after('proctoring_settings');
            }

            // Quiz questions (stored as JSON for simplicity, or we can create a separate table)
            if (!Schema::hasColumn('games', 'quiz_questions')) {
                $table->json('quiz_questions')->nullable()->after('max_attempts');
            }

            // Game images (max 3)
            if (!Schema::hasColumn('games', 'game_images')) {
                $table->json('game_images')->nullable()->after('quiz_questions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'quiz_settings',
                'proctoring_settings',
                'max_attempts',
                'quiz_questions',
                'game_images'
            ]);
        });
    }
};