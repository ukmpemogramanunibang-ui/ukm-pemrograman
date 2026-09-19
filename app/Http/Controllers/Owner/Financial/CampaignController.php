<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\FinancialCampaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = FinancialCampaign::orderBy('created_at', 'desc')->paginate(15);
        return view('owner.financial.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('owner.financial.campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCampaign($request);

        // Set created_by
        $validated['created_by'] = auth()->id();

        // Proses target_data jika target_type bukan 'all'
        if ($request->target_type === 'generation') {
            $validated['target_data'] = ['generation_ids' => $request->input('generation_ids', [])];
        } elseif ($request->target_type === 'specific') {
            $validated['target_data'] = ['user_ids' => $request->input('user_ids', [])];
        } else {
            $validated['target_data'] = null;
        }

        FinancialCampaign::create($validated);

        return redirect()->route('owner.financial.campaigns.index')
            ->with('success', '✅ Kampanye "' . $validated['title'] . '" berhasil dibuat.');
    }

    public function show(FinancialCampaign $campaign)
    {
        return view('owner.financial.campaigns.show', compact('campaign'));
    }

    public function edit(FinancialCampaign $campaign)
    {
        return view('owner.financial.campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, FinancialCampaign $campaign)
    {
        $validated = $this->validateCampaign($request, $campaign);

        // Proses target_data
        if ($request->target_type === 'generation') {
            $validated['target_data'] = ['generation_ids' => $request->input('generation_ids', [])];
        } elseif ($request->target_type === 'specific') {
            $validated['target_data'] = ['user_ids' => $request->input('user_ids', [])];
        } else {
            $validated['target_data'] = null;
        }

        $campaign->update($validated);

        return redirect()->route('owner.financial.campaigns.index')
            ->with('success', '✅ Kampanye "' . $campaign->title . '" berhasil diperbarui.');
    }

    public function destroy(FinancialCampaign $campaign)
    {
        $campaign->delete();
        return back()->with('success', '🗑️ Kampanye berhasil dihapus.');
    }

    // ===== VALIDASI =====
    protected function validateCampaign(Request $request, $campaign = null)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'amount' => 'required|integer|min:0',
            'is_required' => 'boolean',
            'target_type' => 'required|in:all,generation,specific',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'due_date' => 'required|date|after:start_date',
            'status' => 'required|in:draft,active,closed,cancelled',
        ];

        // Jika target_type = generation, tambahkan validasi untuk generation_ids
        if ($request->target_type === 'generation') {
            $rules['generation_ids'] = 'required|array|min:1';
            $rules['generation_ids.*'] = 'exists:generations,id';
        }

        // Jika target_type = specific, tambahkan validasi untuk user_ids
        if ($request->target_type === 'specific') {
            $rules['user_ids'] = 'required|array|min:1';
            $rules['user_ids.*'] = 'exists:users,id';
        }

        $messages = [
            'generation_ids.required' => 'Pilih minimal satu generasi.',
            'generation_ids.*.exists' => 'Generasi yang dipilih tidak valid.',
            'user_ids.required' => 'Pilih minimal satu anggota.',
            'user_ids.*.exists' => 'Anggota yang dipilih tidak valid.',
        ];

        return $request->validate($rules, $messages);
    }
}