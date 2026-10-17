<?php

namespace App\Http\Controllers\Keputusan;

use App\Http\Controllers\Controller;
use App\Models\DecisionPoll;
use App\Models\DecisionPollOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PollingController extends Controller
{
    public function create()
    {
        return view('keputusan.polling.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'description'    => 'nullable|string|max:500',
            'options'        => 'required|array|min:2|max:20',
            'options.*'      => 'required|string|max:80',
            'allow_multiple' => 'boolean',
            'max_choices'    => 'nullable|integer|min:1|max:20',
            'closes_at'      => 'nullable|date|after:now',
        ]);

        $poll = DecisionPoll::create([
            'user_id'        => auth()->id(),
            'public_token'   => strtoupper(Str::random(8)),
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'allow_multiple' => $request->boolean('allow_multiple'),
            'max_choices'    => $data['max_choices'] ?? 1,
            'closes_at'      => $data['closes_at'] ?? null,
            'status'         => 'open',
        ]);

        foreach ($data['options'] as $i => $label) {
            $poll->options()->create(['label' => $label, 'position' => $i]);
        }

        return redirect()->route('keputusan.poll.show', $poll->public_token);
    }

    public function show(string $token)
    {
        $poll = DecisionPoll::where('public_token', $token)
            ->with('options')->firstOrFail();

        return view('keputusan.polling.show', compact('poll'));
    }

    public function vote(Request $request, string $token)
    {
        $poll = DecisionPoll::where('public_token', $token)->firstOrFail();
        abort_unless($poll->isOpen(), 422, 'Polling sudah ditutup.');

        $optionIds = (array) $request->input('option_ids', []);
        abort_if(empty($optionIds), 422, 'Pilih minimal 1 opsi.');

        if (! $poll->allow_multiple && count($optionIds) > 1) {
            abort(422, 'Polling ini hanya menerima 1 pilihan.');
        }

        $fingerprint = hash('sha256', $request->ip() . '|' . $request->userAgent());

        foreach ($optionIds as $id) {
            try {
                \DB::table('decision_poll_votes')->insert([
                    'poll_id'           => $poll->id,
                    'option_id'         => $id,
                    'voter_fingerprint' => $fingerprint,
                    'ip'                => $request->ip(),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
                DecisionPollOption::whereKey($id)->increment('votes');
            } catch (\Throwable $e) {
                // sudah pernah vote, skip
            }
        }

        return back()->with('success', 'Suara Anda tersimpan.');
    }
}