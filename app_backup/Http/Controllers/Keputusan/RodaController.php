<?php

namespace App\Http\Controllers\Keputusan;

use App\Http\Controllers\Controller;
use App\Models\DecisionWheel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RodaController extends Controller
{
    public function create()
    {
        return view('keputusan.roda.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => ['required', 'string', 'max:120'],
            'selection_mode'  => ['required', 'in:weighted,secret_random'],
            'duration'        => ['required', 'integer', 'in:10,30,60,120,150'],
            'result_mode'     => ['required', 'in:single,multi,ranking'],
            'winner_count'    => ['nullable', 'integer', 'min:1', 'max:50'],
            'result_template' => ['required', 'string', 'max:160'],
            'sound_mode'      => ['required', 'in:system,upload,none'],
            'sound_file'      => ['nullable', 'file', 'mimes:mp3,wav,ogg', 'max:5120'],

            'entries'          => ['required', 'array', 'min:2', 'max:50'],
            'entries.*.name'   => ['required', 'string', 'max:100'],
            'entries.*.weight' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $entries = $data['entries'];

        if ($data['selection_mode'] === 'weighted') {
            $total = collect($entries)->sum(fn ($e) => (int) ($e['weight'] ?? 0));

            if ($total !== 100) {
                return back()->withInput()
                    ->withErrors(['entries' => "Total bobot harus 100%. Saat ini {$total}%."]);
            }

            $has100 = collect($entries)->contains(fn ($e) => (int) ($e['weight'] ?? 0) === 100);
            if ($has100) {
                $invalid = collect($entries)->contains(function ($e) {
                    $w = (int) ($e['weight'] ?? 0);
                    return $w !== 0 && $w !== 100;
                });
                if ($invalid) {
                    return back()->withInput()
                        ->withErrors(['entries' => 'Jika satu peserta 100%, peserta lain harus 0%.']);
                }
            }
        } else {
            foreach ($entries as &$e) { $e['weight'] = 0; }
            unset($e);
        }

        $soundPath = null;
        if ($data['sound_mode'] === 'upload' && $request->hasFile('sound_file')) {
            $soundPath = $request->file('sound_file')->store('wheel-sounds', 'public');
        }

        $controllerToken = Str::random(32);

        $wheel = DecisionWheel::create([
            'user_id'               => auth()->id(),
            'public_token'          => strtoupper(Str::random(10)),
            'controller_token_hash' => hash('sha256', $controllerToken),
            'title'                 => $data['title'],
            'selection_mode'        => $data['selection_mode'],
            'result_mode'           => $data['result_mode'],
            'winner_count'          => $data['winner_count'] ?? 1,
            'duration'              => $data['duration'],
            'sound_mode'            => $data['sound_mode'],
            'sound_path'            => $soundPath,
            'result_template'       => $data['result_template'],
            'status'                => 'ready',
        ]);

        foreach ($entries as $i => $entry) {
            $wheel->entries()->create([
                'name'     => $entry['name'],
                'weight'   => (int) ($entry['weight'] ?? 0),
                'position' => $i,
            ]);
        }

        session([
            'controller_token'               => $controllerToken,
            'controller_token_' . $wheel->id => $controllerToken,
        ]);

        return redirect()->route('keputusan.roda.show', $wheel->public_token)
            ->with('success', 'Roda berhasil dibuat.');
    }

    public function show(Request $request, string $token)
    {
        $wheel = DecisionWheel::where('public_token', $token)
            ->with([
                'entries',
                'spins'        => fn ($q) => $q->latest('spin_number')->take(10),
                'spins.winner', // ✅ FIX LAZY LOADING
            ])
            ->firstOrFail();

        $controllerToken = $request->session()->get('controller_token_' . $wheel->id)
            ?? $request->session()->get('controller_token');

        $isOperator = $wheel->isOperator($controllerToken)
            || ($wheel->user_id && $wheel->user_id === auth()->id());

        return view('keputusan.roda.show', [
            'wheel'           => $wheel,
            'isOperator'      => $isOperator,
            'controllerToken' => $isOperator ? $controllerToken : null,
        ]);
    }
}