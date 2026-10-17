<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\PenaltyRule;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index()
    {
        $rules = PenaltyRule::orderBy('level')->get();
        return view('owner.financial.penalty-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('owner.financial.penalty-rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'start_day' => 'required|integer|min:0',
            'end_day' => 'nullable|integer|min:0|gte:start_day',
            'fixed_amount' => 'nullable|integer|min:0',
            'daily_amount' => 'nullable|integer|min:0',
            'max_amount' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        PenaltyRule::create($validated);

        return redirect()->route('owner.financial.penalty-rules.index')
            ->with('success', 'Aturan denda berhasil dibuat.');
    }

    public function edit(PenaltyRule $penaltyRule)
    {
        return view('owner.financial.penalty-rules.edit', compact('penaltyRule'));
    }

    public function update(Request $request, PenaltyRule $penaltyRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1',
            'start_day' => 'required|integer|min:0',
            'end_day' => 'nullable|integer|min:0|gte:start_day',
            'fixed_amount' => 'nullable|integer|min:0',
            'daily_amount' => 'nullable|integer|min:0',
            'max_amount' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $penaltyRule->update($validated);

        return redirect()->route('owner.financial.penalty-rules.index')
            ->with('success', 'Aturan denda berhasil diperbarui.');
    }

    public function destroy(PenaltyRule $penaltyRule)
    {
        $penaltyRule->delete();
        return back()->with('success', 'Aturan denda berhasil dihapus.');
    }
}