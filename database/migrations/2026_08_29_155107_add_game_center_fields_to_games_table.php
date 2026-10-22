<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Cek apakah kolom sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('games', 'source_type')) {
                $table->enum('source_type', ['external', 'native'])
                      ->default('native')
                      ->after('status');
            }
            if (!Schema::hasColumn('games', 'game_type')) {
                $table->enum('game_type', ['typing', 'quiz', 'coding', 'logic', 'reaction', 'custom'])
                      ->nullable()->after('source_type');
            }
            if (!Schema::hasColumn('games', 'url')) {
                $table->string('url')->nullable()->after('game_type');
            }
            if (!Schema::hasColumn('games', 'integration_level')) {
                $table->enum('integration_level', ['link', 'embedded', 'api'])
                      ->default('link')->nullable()->after('url');
            }
            if (!Schema::hasColumn('games', 'banner')) {
                $table->string('banner')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('games', 'developer')) {
                $table->string('developer')->nullable()->after('banner');
            }
            if (!Schema::hasColumn('games', 'difficulty')) {
                $table->enum('difficulty', ['beginner', 'intermediate', 'advanced', 'expert'])
                      ->nullable()->after('developer');
            }
            if (!Schema::hasColumn('games', 'game_settings')) {
                $table->json('game_settings')->nullable()->after('difficulty');
            }
            if (!Schema::hasColumn('games', 'visits')) {
                $table->unsignedBigInteger('visits')->default(0)->after('game_settings');
            }
            if (!Schema::hasColumn('games', 'show_in_leaderboard')) {
                $table->boolean('show_in_leaderboard')->default(true)->after('visits');
            }
            if (!Schema::hasColumn('games', 'support_rating')) {
                $table->boolean('support_rating')->default(false)->after('show_in_leaderboard');
            }
            if (!Schema::hasColumn('games', 'support_matchmaking')) {
                $table->boolean('support_matchmaking')->default(false)->after('support_rating');
            }
            if (!Schema::hasColumn('games', 'support_challenge')) {
                $table->boolean('support_challenge')->default(false)->after('support_matchmaking');
            }
            if (!Schema::hasColumn('games', 'total_challenges')) {
                $table->unsignedInteger('total_challenges')->default(0)->after('support_challenge');
            }
            if (!Schema::hasColumn('games', 'total_competitions')) {
                $table->unsignedInteger('total_competitions')->default(0)->after('total_challenges');
            }
            if (!Schema::hasColumn('games', 'total_sessions')) {
                $table->unsignedInteger('total_sessions')->default(0)->after('total_competitions');
            }
            if (!Schema::hasColumn('games', 'last_played_at')) {
                $table->timestamp('last_played_at')->nullable()->after('total_sessions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'source_type', 'game_type', 'url', 'integration_level',
                'banner', 'developer', 'difficulty', 'game_settings',
                'visits', 'show_in_leaderboard', 'support_rating',
                'support_matchmaking', 'support_challenge',
                'total_challenges', 'total_competitions',
                'total_sessions', 'last_played_at'
            ]);
        });
    }
};