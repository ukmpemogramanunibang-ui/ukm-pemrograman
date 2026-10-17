<?php

namespace App\Services\GameTesting;

use App\Models\Game;
use App\Services\GameTesting\Results\DiagnosticResult;
use App\Services\GameTesting\Engines\TypingTest;
use App\Services\GameTesting\Engines\QuizTest;
use App\Services\GameTesting\Engines\TebakTest;
use App\Services\GameTesting\Engines\LogicTest;
use App\Services\GameTesting\Engines\CodingTest;
use App\Services\GameTesting\Engines\ReactionTest;
use App\Services\GameTesting\Engines\CustomTest;
use Illuminate\Support\Str;

class GameDiagnosticService
{
    private array $engineMap = [
        'typing'   => TypingTest::class,
        'quiz'     => QuizTest::class,
        'tebak'    => TebakTest::class,
        'logic'    => LogicTest::class,
        'coding'   => CodingTest::class,
        'reaction' => ReactionTest::class,
        'custom'   => CustomTest::class,
    ];

    public function diagnose(Game $game, ?string $token = null): DiagnosticResult
    {
        $result = new DiagnosticResult();

        // Generate token jika belum ada
        if (!$token) {
            $token = 'TEST-' . strtoupper(Str::random(10));
        }
        $result->token = $token;
        $result->sessionId = 'SESSION-' . Str::random(8);

        // Ambil engine test
        $engineClass = $this->engineMap[$game->game_type] ?? null;
        if (!$engineClass) {
            $result->addCheck('engine', 'Game Engine', 'critical', 'Engine "' . $game->game_type . '" tidak dikenali');
            $result->setOverall();
            return $result;
        }

        $engine = new $engineClass();

        // 1. Static Scan
        $checks = $engine->staticScan($game);
        foreach ($checks as $check) {
            $result->addCheck(
                $check['key'],
                $check['name'],
                $check['status'],
                $check['message'] ?? null,
                $check['details'] ?? []
            );
        }

        // 2. Ambil data game
        $gameData = [
            'settings' => $game->game_settings ?? $game->typing_settings ?? $game->quiz_settings ?? [],
            'questions' => $game->quiz_questions ?? $game->game_questions ?? [],
        ];

        // 3. Runtime Test
        $runtimeSteps = $engine->runtimeTest($game, $gameData);
        foreach ($runtimeSteps as $step) {
            $result->addRuntime($step['step'], $step['status'], $step['message'] ?? null);
        }

        // 4. Player Simulation
        $simSteps = $engine->playerSimulation($game, $gameData);
        foreach ($simSteps as $step) {
            $result->addSimulation($step['step'], $step['status'], $step['message'] ?? null);
        }

        // 5. Detail Game
        $result->gameDetails = [
            'id' => $game->id,
            'name' => $game->name,
            'slug' => $game->slug,
            'icon' => $game->icon ?? '🎮',
            'version' => $game->version ?? '1.0.0',
            'source_type' => $game->source_type,
            'game_type' => $game->game_type,
            'status' => $game->status,
            'difficulty' => $game->difficulty ?? 'intermediate',
            'description' => $game->description ?? 'Tidak ada deskripsi',
            'developer' => $game->developer ?? 'UNICODE',
            'player_count' => $game->player_count ?? 0,
            'total_matches' => $game->total_matches ?? 0,
            'visits' => $game->visits ?? 0,
            'support_rating' => (bool) ($game->support_rating ?? false),
            'support_matchmaking' => (bool) ($game->support_matchmaking ?? false),
            'support_challenge' => (bool) ($game->support_challenge ?? false),
            'show_in_leaderboard' => (bool) ($game->show_in_leaderboard ?? false),
            'total_challenges' => $game->total_challenges ?? 0,
            'total_competitions' => $game->total_competitions ?? 0,
            'total_sessions' => $game->total_sessions ?? 0,
            'last_played_at' => $game->last_played_at ? $game->last_played_at->format('d M Y H:i') : 'Belum pernah',
            'created_at' => $game->created_at->format('d M Y H:i'),
            'updated_at' => $game->updated_at->format('d M Y H:i'),
            'url' => $game->url,
            'integration_level' => $game->integration_level ?? 'link',
            'image_count' => is_array($game->game_images) ? count($game->game_images) : 0,
            'question_count' => is_array($game->game_questions ?? $game->quiz_questions ?? []) ? count($game->game_questions ?? $game->quiz_questions ?? []) : 0,
            'settings' => $game->game_settings ?? $game->typing_settings ?? $game->quiz_settings ?? [],
        ];

        // 6. Tentukan overall status
        $result->setOverall();

        return $result;
    }
}