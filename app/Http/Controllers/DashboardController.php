<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ============================================================
        // STATISTIK DASAR
        // ============================================================
        $totalGames        = Game::count();
        $totalPlayers      = Player::count();
        $activePlayers     = Player::where('status', 'active')->count();
        $totalChallenges   = Challenge::count();
        $activeChallenges  = Challenge::where('is_active', true)->count();
        $totalCompetitions = Competition::count();
        $activeCompetitions = Competition::whereIn('status', ['registration', 'live'])->count();
        $totalMatches      = GameMatch::count();
        $matchesToday      = GameMatch::whereDate('created_at', today())->count();
        $finishedMatches   = GameMatch::where('status', 'finished')->count();
        $totalSessions     = GameSession::count();

        // ============================================================
        // REWARD TOTAL
        // ============================================================
        $totalReward = Competition::sum('prize_pool') ?? 0;
        $totalRewardFormatted = 'Rp ' . number_format($totalReward, 0, ',', '.');

        // ============================================================
        // STATUS SERVER & MATCHMAKING
        // ============================================================
        $hasWaitingMatches = GameMatch::whereIn('status', ['waiting', 'active'])->exists();
        $matchmakingStatus = $hasWaitingMatches ? 'Aktif' : 'Tidak Ada Antrian';
        $matchmakingColor = $hasWaitingMatches ? 'emerald' : 'amber';

        $serverStatus = 'Online';
        $serverColor = 'cyan';

        // ============================================================
        // RATA-RATA WIN RATE
        // ============================================================
        $averageWinRate = Player::avg('win_rate') ?? 0;
        $averageWinRateFormatted = number_format($averageWinRate, 1) . '%';

        // ============================================================
        // PEMAIN TERBAIK
        // ============================================================
        $topPlayer = Player::orderBy('rating', 'desc')->first();
        $topPlayerName = $topPlayer?->name ?? 'Belum ada';
        $topPlayerRating = $topPlayer?->rating ?? '-';
        $topPlayerRank = $topPlayer?->rank ?? '-';

        // ============================================================
        // GAME POPULER (pakai relasi sessions)
        // ============================================================
        $popularGames = Game::withCount('sessions')
            ->orderBy('sessions_count', 'desc')
            ->limit(6)
            ->get();

        // ============================================================
        // AKTIVITAS TERBARU
        // ============================================================
        $recentMatches = GameMatch::with(['game', 'winner'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentActivities = $recentMatches->map(function ($match) {
            return [
                'player' => $match->winner?->name ?? 'Pemain',
                'action' => 'menyelesaikan pertandingan di ' . ($match->game?->name ?? 'game'),
                'time'   => $match->created_at->diffForHumans(),
            ];
        });

        // ============================================================
        // KIRIM KE VIEW
        // ============================================================
        return view('owner.game-center.dashboard', compact(
            'totalGames',
            'totalPlayers',
            'activePlayers',
            'totalChallenges',
            'activeChallenges',
            'totalCompetitions',
            'activeCompetitions',
            'totalMatches',
            'matchesToday',
            'finishedMatches',
            'totalSessions',
            'totalRewardFormatted',
            'matchmakingStatus',
            'matchmakingColor',
            'serverStatus',
            'serverColor',
            'averageWinRateFormatted',
            'averageWinRate',
            'topPlayerName',
            'topPlayerRating',
            'topPlayerRank',
            'popularGames',
            'recentActivities'
        ));
    }
}