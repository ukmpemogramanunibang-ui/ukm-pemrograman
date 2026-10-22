<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGameStat;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nim', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('rank')) {
            $query->where('rank', $request->rank);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $players = $query->paginate(15);

        return view('owner.game-center.players.index', compact('players'));
    }

    public function show(User $player)
    {
        // Ambil statistik game dari user
        $gameStats = UserGameStat::where('user_id', $player->id)
            ->with('game')
            ->get();

        // Total main
        $totalPlays = $gameStats->sum('total_plays');
        $totalScore = $gameStats->sum('total_score');
        $bestScore = $gameStats->max('best_score');

        return view('owner.game-center.players.show', compact('player', 'gameStats', 'totalPlays', 'totalScore', 'bestScore'));
    }

    public function edit(User $player)
    {
        return view('owner.game-center.players.edit', compact('player'));
    }

    public function update(Request $request, User $player)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $player->id,
            'rank' => 'nullable|string|in:Bronze,Silver,Gold,Platinum,Diamond,Master,Grandmaster',
            'xp' => 'nullable|integer|min:0',
            'rating' => 'nullable|integer|min:0',
            'win_rate' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:active,suspended,banned',
        ]);

        $player->update($validated);

        return redirect()->route('owner.game.players.index')
            ->with('success', 'Data pemain berhasil diperbarui.');
    }

    public function destroy(User $player)
    {
        $player->delete();
        return redirect()->route('owner.game.players.index')
            ->with('success', 'Pemain berhasil dihapus.');
    }
}