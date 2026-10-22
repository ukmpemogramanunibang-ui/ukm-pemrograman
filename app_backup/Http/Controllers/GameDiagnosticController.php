<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\GameTesting\GameDiagnosticService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameDiagnosticController extends Controller
{
    protected GameDiagnosticService $diagnosticService;

    public function __construct(GameDiagnosticService $diagnosticService)
    {
        $this->diagnosticService = $diagnosticService;
    }

    public function index(Request $request, Game $game)
    {
        // Generate token baru setiap scan
        $token = 'TEST-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(5));

        // Jalankan diagnostic
        $result = $this->diagnosticService->diagnose($game, $token);

        // Simpan hasil diagnostic ke database (perbaiki bagian ini)
        $game->diagnostic_status = $result->overall;

        // Filter array secara manual, bukan pakai Collection
        $failedChecks = array_filter($result->checks, function($check) {
            return in_array($check['status'], ['failed', 'critical']);
        });
        $game->diagnostic_errors = array_column($failedChecks, 'message');

        $game->diagnostic_last_run = now();
        $game->save();

        // Ambil daftar game untuk dropdown (opsional)
        $games = Game::orderBy('name')->get();

        return view('owner.game-center.diagnostic.index', [
            'game' => $game,
            'result' => $result,
            'games' => $games,
            'token' => $token,
        ]);
    }

    public function rescan(Request $request, Game $game)
    {
        return $this->index($request, $game);
    }

    public function downloadReport(Request $request, Game $game)
    {
        $token = 'TEST-' . strtoupper(Str::random(6)) . '-' . strtoupper(Str::random(5));
        $result = $this->diagnosticService->diagnose($game, $token);

        $data = [
            'game' => $game,
            'result' => $result,
            'generated_at' => now()->format('d M Y H:i:s'),
        ];

        return view('owner.game-center.diagnostic.report-pdf', $data);
    }
}