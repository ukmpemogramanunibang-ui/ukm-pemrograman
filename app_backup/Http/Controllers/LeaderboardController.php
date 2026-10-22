<?php

namespace App\Http\Controllers;

use App\Models\Leaderboard;
use App\Models\Game;
use App\Models\GameSession;
use App\Models\LeaderboardEntry;
use App\Models\UserGameStat;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::where('status', 'published')->get();
        $period = $request->period ?? 'global';
        $gameId = $request->game_id ?? null;

        $leaderboard = Leaderboard::with(['entries.player'])
            ->when($gameId, fn($q) => $q->where('game_id', $gameId))
            ->where('period', $period)
            ->first();

        if (!$leaderboard) {
            $this->rebuildLeaderboard($period, $gameId);
            $leaderboard = Leaderboard::with(['entries.player'])
                ->when($gameId, fn($q) => $q->where('game_id', $gameId))
                ->where('period', $period)
                ->first();
        }

        return view('owner.game-center.leaderboard.index', compact('leaderboard', 'games', 'period', 'gameId'));
    }

    public function getLeaderboard(Request $request, $gameId)
    {
        try {
            $game = Game::findOrFail($gameId);
            $userId = auth()->id();

            // Ambil top 10 dari leaderboard entry
            $leaderboard = Leaderboard::where('game_id', $gameId)
                ->where('period', 'global')
                ->first();

            $topPlayers = [];
            if ($leaderboard) {
                $entries = LeaderboardEntry::where('leaderboard_id', $leaderboard->id)
                    ->with('player')
                    ->orderBy('rank')
                    ->limit(10)
                    ->get();

                foreach ($entries as $entry) {
                    $stats = UserGameStat::where('user_id', $entry->player_id)
                        ->where('game_id', $gameId)
                        ->first();

                    $topPlayers[] = [
                        'rank' => $entry->rank,
                        'player_id' => $entry->player_id,
                        'player_name' => $entry->player->name ?? 'Guest',
                        'score' => $entry->score,
                        'total_plays' => $stats->total_plays ?? 0,
                        'best_score' => $stats->best_score ?? 0,
                        'avg_score' => $stats->avg_score ?? 0,
                    ];
                }
            }

            // Posisi user
            $userRank = null;
            $userScore = null;
            $userStats = null;
            if ($userId) {
                $userStats = UserGameStat::where('user_id', $userId)
                    ->where('game_id', $gameId)
                    ->first();

                if ($userStats) {
                    $userScore = $userStats->best_score;
                    // Hitung rank dari leaderboard
                    if ($leaderboard) {
                        $rank = LeaderboardEntry::where('leaderboard_id', $leaderboard->id)
                            ->where('score', '>', $userScore)
                            ->count() + 1;
                        $userRank = $rank;
                    }
                }
            }

            return response()->json([
                'top_players' => $topPlayers,
                'user_rank' => $userRank,
                'user_score' => $userScore,
                'user_stats' => $userStats,
                'total_players' => $leaderboard ? LeaderboardEntry::where('leaderboard_id', $leaderboard->id)->count() : 0,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function rebuild(Request $request)
    {
        $period = $request->period ?? 'global';
        $gameId = $request->game_id ?? null;
        $this->rebuildLeaderboard($period, $gameId);
        return back()->with('success', 'Leaderboard berhasil dibangun ulang.');
    }

    private function rebuildLeaderboard($period = 'global', $gameId = null)
    {
        // Hapus leaderboard lama
        $query = Leaderboard::where('period', $period);
        if ($gameId) $query->where('game_id', $gameId);
        $query->delete();

        // Ambil data statistik user per game
        $statsQuery = UserGameStat::where('game_id', $gameId)
            ->orderBy('best_score', 'desc');

        $stats = $statsQuery->get();
        if ($stats->isEmpty()) return;

        $leaderboard = Leaderboard::create([
            'game_id' => $gameId,
            'period' => $period,
            'calculated_at' => now(),
        ]);

        $rank = 1;
        foreach ($stats as $stat) {
            LeaderboardEntry::create([
                'leaderboard_id' => $leaderboard->id,
                'player_id' => $stat->user_id,
                'rank' => $rank++,
                'score' => $stat->best_score,
            ]);
        }
    }
}