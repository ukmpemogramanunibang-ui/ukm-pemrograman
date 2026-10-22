<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Game;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index(Request $request)
    {
        $challenges = Challenge::with('game')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('owner.game-center.challenges.index', compact('challenges'));
    }

    public function create()
    {
        $games = Game::where('status', 'published')->get();
        return view('owner.game-center.challenges.create', compact('games'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'game_id' => 'required|exists:games,id',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'xp_reward' => 'required|integer|min:0',
            'max_participants' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'icon' => 'nullable|string|max:10',
        ]);

        $challenge = Challenge::create($validated);

        return redirect()->route('owner.game.challenges.index')
            ->with('success', 'Challenge berhasil dibuat.');
    }

    public function show(Challenge $challenge)
    {
        return view('owner.game-center.challenges.show', compact('challenge'));
    }

    public function edit(Challenge $challenge)
    {
        $games = Game::where('status', 'published')->get();
        return view('owner.game-center.challenges.edit', compact('challenge', 'games'));
    }

    public function update(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'game_id' => 'required|exists:games,id',
            'difficulty' => 'required|in:easy,medium,hard,expert',
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'xp_reward' => 'required|integer|min:0',
            'max_participants' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'icon' => 'nullable|string|max:10',
        ]);

        $challenge->update($validated);

        return redirect()->route('owner.game.challenges.index')
            ->with('success', 'Challenge berhasil diperbarui.');
    }

    public function destroy(Challenge $challenge)
    {
        $challenge->delete();
        return redirect()->route('owner.game.challenges.index')
            ->with('success', 'Challenge berhasil dihapus.');
    }

    public function toggle(Challenge $challenge)
    {
        $challenge->update(['is_active' => !$challenge->is_active]);
        $status = $challenge->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('owner.game.challenges.index')
            ->with('success', "Challenge berhasil {$status}.");
    }
}