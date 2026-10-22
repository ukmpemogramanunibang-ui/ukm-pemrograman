<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Game;
use App\Models\CompetitionParticipant;
use App\Models\CompetitionMatch;
use App\Models\CompetitionRound;
use App\Models\CompetitionSponsor;
use App\Services\BracketGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::with(['game', 'participants', 'rounds'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => Competition::count(),
            'active' => Competition::where('status', 'active')->count(),
            'live' => Competition::where('status', 'live')->count(),
            'finished' => Competition::where('status', 'finished')->count(),
        ];

        return view('owner.game-center.competitions.index', compact('competitions', 'stats'));
    }

    public function create()
    {
        $games = Game::where('status', 'published')->get();
        return view('owner.game-center.competitions.create', compact('games'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'description' => 'nullable|string',
            'type' => 'required|in:knockout,round_robin,swiss',
            'participant_type' => 'required|in:individual,team,group',
            'max_participants' => 'required|integer|min:2|max:256',
            'winner_count' => 'required|integer|min:1|max:10',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'anti_cheat_enabled' => 'boolean',
            'auto_matchmaking' => 'boolean',
            'allow_admin_override' => 'boolean',
            'sponsors' => 'nullable|array',
            'sponsors.*.name' => 'required|string',
            'sponsors.*.logo' => 'nullable|string',
            'sponsors.*.url' => 'nullable|url',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';
        $validated['anti_cheat_enabled'] = $request->boolean('anti_cheat_enabled', true);
        $validated['auto_matchmaking'] = $request->boolean('auto_matchmaking', true);
        $validated['allow_admin_override'] = $request->boolean('allow_admin_override', false);

        $competition = Competition::create($validated);

        // Simpan sponsor
        if ($request->has('sponsors')) {
            foreach ($request->sponsors as $index => $sponsorData) {
                if (!empty($sponsorData['name'])) {
                    CompetitionSponsor::create([
                        'competition_id' => $competition->id,
                        'name' => $sponsorData['name'],
                        'logo' => $sponsorData['logo'] ?? null,
                        'url' => $sponsorData['url'] ?? null,
                        'position' => $index + 1,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('owner.game.competitions.show', $competition)
            ->with('success', 'Kompetisi berhasil dibuat!');
    }

    public function show(Competition $competition)
    {
        $competition->load(['game', 'participants.user', 'rounds.matches', 'sponsors']);
        $bracket = $this->buildBracket($competition);
        $ranking = $this->buildRanking($competition);

        return view('owner.game-center.competitions.show', compact('competition', 'bracket', 'ranking'));
    }

    public function edit(Competition $competition)
    {
        $games = Game::where('status', 'published')->get();
        return view('owner.game-center.competitions.edit', compact('competition', 'games'));
    }

    public function update(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'description' => 'nullable|string',
            'type' => 'required|in:knockout,round_robin,swiss',
            'participant_type' => 'required|in:individual,team,group',
            'max_participants' => 'required|integer|min:2|max:256',
            'winner_count' => 'required|integer|min:1|max:10',
            'status' => 'required|in:draft,registration,active,live,finished,cancelled',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'anti_cheat_enabled' => 'boolean',
            'auto_matchmaking' => 'boolean',
            'allow_admin_override' => 'boolean',
        ]);

        $competition->update($validated);

        return redirect()->route('owner.game.competitions.show', $competition)
            ->with('success', 'Kompetisi berhasil diperbarui!');
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();
        return redirect()->route('owner.game.competitions.index')
            ->with('success', 'Kompetisi berhasil dihapus.');
    }

    public function addParticipants(Request $request, Competition $competition)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->user_ids as $userId) {
            CompetitionParticipant::create([
                'competition_id' => $competition->id,
                'user_id' => $userId,
                'seed' => CompetitionParticipant::where('competition_id', $competition->id)->count() + 1,
                'status' => 'registered',
            ]);
        }

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function generateBracket(Competition $competition)
    {
        // Hapus bracket lama
        CompetitionMatch::where('competition_id', $competition->id)->delete();
        CompetitionRound::where('competition_id', $competition->id)->delete();

        $generator = new BracketGenerator();
        $generator->generate($competition);

        // Update status
        $competition->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Bracket berhasil digenerate!');
    }

    public function startMatch(Competition $competition, CompetitionMatch $match)
    {
        if ($match->status !== 'upcoming') {
            return redirect()->back()->with('error', 'Match tidak dapat dimulai.');
        }

        // Cek kedua peserta
        if (!$match->participant_a_id || !$match->participant_b_id) {
            return redirect()->back()->with('error', 'Peserta tidak lengkap.');
        }

        // Buat game session
        $gameSession = GameSession::create([
            'player_id' => $match->participantA->user_id,
            'game_id' => $competition->game_id,
            'score' => 0,
            'mode' => 'competition',
            'result' => 'pending',
            'started_at' => now(),
        ]);

        $match->update([
            'status' => 'active',
            'started_at' => now(),
            'game_session_id' => $gameSession->id,
        ]);

        return redirect()->route('owner.game.competitions.match', [$competition, $match])
            ->with('success', 'Match dimulai!');
    }

    public function viewMatch(Competition $competition, CompetitionMatch $match)
    {
        $match->load(['participantA.user', 'participantB.user', 'winner', 'gameSession']);
        $antiCheatEvents = $match->antiCheatEvents ?? [];

        return view('owner.game-center.competitions.match', compact('competition', 'match', 'antiCheatEvents'));
    }

    public function updateMatchScore(Request $request, Competition $competition, CompetitionMatch $match)
    {
        $request->validate([
            'score_a' => 'required|numeric|min:0',
            'score_b' => 'required|numeric|min:0',
        ]);

        $match->update([
            'score_a' => $request->score_a,
            'score_b' => $request->score_b,
        ]);

        // Tentukan pemenang
        if ($request->score_a > $request->score_b) {
            $match->update(['winner_id' => $match->participant_a_id, 'loser_id' => $match->participant_b_id]);
        } elseif ($request->score_b > $request->score_a) {
            $match->update(['winner_id' => $match->participant_b_id, 'loser_id' => $match->participant_a_id]);
        }

        // Update status participant
        if ($match->winner_id) {
            $winner = $match->winner;
            $winner->update(['status' => 'active', 'total_score' => $winner->total_score + max($request->score_a, $request->score_b)]);
            $loser = $match->loser;
            if ($loser) {
                $loser->update(['status' => 'eliminated', 'eliminated_round' => $match->round_id]);
            }
        }

        return redirect()->route('owner.game.competitions.show', $competition)
            ->with('success', 'Skor berhasil diperbarui!');
    }

    public function getLiveData(Competition $competition)
    {
        $activeMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('status', 'active')
            ->with(['participantA.user', 'participantB.user'])
            ->get();

        return response()->json([
            'live' => $activeMatches,
            'has_live' => $activeMatches->count() > 0,
        ]);
    }

    private function buildBracket($competition)
    {
        $rounds = $competition->rounds()->with('matches.participantA.user', 'matches.participantB.user', 'matches.winner')->get();

        $bracket = [];
        foreach ($rounds as $round) {
            $bracket[$round->round_number] = [
                'name' => $round->name,
                'matches' => $round->matches->map(function ($match) {
                    return [
                        'id' => $match->id,
                        'match_number' => $match->match_number,
                        'participant_a' => $match->participantA ? $match->participantA->user->name : null,
                        'participant_b' => $match->participantB ? $match->participantB->user->name : null,
                        'score_a' => $match->score_a,
                        'score_b' => $match->score_b,
                        'status' => $match->status,
                        'status_color' => $match->status_color,
                        'winner' => $match->winner ? $match->winner->user->name : null,
                    ];
                }),
            ];
        }

        return $bracket;
    }

    private function buildRanking($competition)
    {
        return $competition->participants()
            ->with('user')
            ->orderBy('total_score', 'desc')
            ->orderBy('final_rank', 'asc')
            ->get()
            ->map(function ($participant, $index) {
                $participant->rank = $index + 1;
                return $participant;
            });
    }
}