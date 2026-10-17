<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\FinancialCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Support\Concerns\ResolvesFinancialLookups; 

class CampaignController extends Controller
{
    use ResolvesFinancialLookups;
    /**
     * Status yang valid untuk kampanye.
     */
    public const STATUSES = ['draft', 'active', 'closed', 'cancelled'];

    /**
     * Kategori yang valid (untuk konsistensi antar form & filter).
     */
    public const CATEGORIES = [
        'id_card', 'event', 'reuni', 'seragam', 'study_tour', 'donasi', 'lainnya',
    ];

    // ═════════════════════════════════════════════════════════════
    // INDEX — daftar kampanye dengan filter, tab, dan summary
    // ═════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $base = FinancialCampaign::query();

        $tab = $request->get('tab', 'all');
        if (!in_array($tab, ['all','active','draft','closed','overdue'], true)) {
            $tab = 'all';
        }

        $query = (clone $base);

        // ─── Tab filter ───
        match ($tab) {
            'active'  => $query->where('status', 'active'),
            'draft'   => $query->where('status', 'draft'),
            'closed'  => $query->where('status', 'closed'),
            'overdue' => $query->where('status', 'active')
                               ->whereNotNull('due_date')
                               ->whereDate('due_date', '<', now()->toDateString()),
            default   => null,
        };

        // ─── Search ───
        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // ─── Kategori ───
        if ($cat = $request->input('category')) {
            if (in_array($cat, self::CATEGORIES, true)) {
                $query->where('category', $cat);
            }
        }

        // ─── Sort ───
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'deadline' => $query->orderByRaw('due_date IS NULL, due_date ASC'),
            'amount'   => $query->orderByDesc('amount'),
            'title'    => $query->orderBy('title'),
            'progress' => $this->hasColumn('collected_amount') && $this->hasColumn('target_amount')
                            ? $query->orderByRaw('(collected_amount / NULLIF(target_amount, 0)) DESC')
                            : $query->orderByDesc('created_at'),
            default    => $query->orderByDesc('created_at'),
        };

        $campaigns = $query->paginate(12)->withQueryString();

        // ─── Summary (cached 60 detik untuk performa) ───
        $summary = Cache::remember('financial.campaigns.summary', 60, function () {
            $hasCollected   = $this->hasColumn('collected_amount');
            $hasTarget      = $this->hasColumn('target_amount');
            $hasParticipants= $this->hasColumn('participants_count');

            return [
                'total'        => FinancialCampaign::count(),
                'active'       => FinancialCampaign::where('status', 'active')->count(),
                'draft'        => FinancialCampaign::where('status', 'draft')->count(),
                'closed'       => FinancialCampaign::where('status', 'closed')->count(),
                'cancelled'    => FinancialCampaign::where('status', 'cancelled')->count(),
                'collected'    => $hasCollected ? (float) FinancialCampaign::sum('collected_amount') : 0,
                'target'       => $hasTarget    ? (float) FinancialCampaign::sum('target_amount')    : 0,
                'participants' => $hasParticipants ? (int) FinancialCampaign::sum('participants_count') : 0,
                'overdue'      => FinancialCampaign::where('status', 'active')
                                                   ->whereNotNull('due_date')
                                                   ->whereDate('due_date', '<', now()->toDateString())
                                                   ->count(),
            ];
        });

        return view('owner.financial.campaigns.index', compact('campaigns', 'summary'));
    }

    // ═════════════════════════════════════════════════════════════
    // CREATE
    // ═════════════════════════════════════════════════════════════
  public function create()
    {
        return view('owner.financial.campaigns.create', [
            'categories'  => self::CATEGORIES,
            'statuses'    => self::STATUSES,
            'generations' => $this->resolveGenerations(),   // ⬅️ dari trait
            'members'     => $this->resolveMembers(),        // ⬅️ dari trait
            'debug'       => app()->hasDebugModeEnabled(),   // ⬅️ sembunyikan di prod
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // STORE
    // ═════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $validated = $this->validateCampaign($request);
        $validated['created_by']  = auth()->id();
        $validated['target_data'] = $this->buildTargetData($request);

        try {
            DB::beginTransaction();

            $campaign = FinancialCampaign::create($validated);

            DB::commit();
            $this->flushSummaryCache();

            return redirect()
                ->route('owner.financial.campaigns.show', $campaign)
                ->with('success', "✅ Kampanye \"{$campaign->title}\" berhasil dibuat.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[CampaignController@store] gagal', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal membuat kampanye: ' . $e->getMessage());
        }
    }

    // ═════════════════════════════════════════════════════════════
    // SHOW
    // ═════════════════════════════════════════════════════════════
    public function show(FinancialCampaign $campaign)
    {
        // Eager-load relasi bila tersedia (aman walau belum ada)
        $with = ['creator'];
        foreach ($with as $rel) {
            if (method_exists($campaign, $rel)) {
                $campaign->load($rel);
            }
        }

        // Statistik kampanye individual (aman bila kolom tidak ada)
        $stats = [
            'target'       => (float) ($campaign->target_amount    ?? $campaign->amount ?? 0),
            'collected'    => (float) ($campaign->collected_amount ?? 0),
            'participants' => (int)   ($campaign->participants_count ?? 0),
            'expected'     => (int)   ($campaign->expected_participants ?? 0),
        ];
        $stats['progress_pct'] = $stats['target'] > 0
            ? min(100, round(($stats['collected'] / $stats['target']) * 100, 1))
            : 0;

        // Target breakdown
        $targetBreakdown = $this->resolveTargetBreakdown($campaign);

        return view('owner.financial.campaigns.show', compact('campaign', 'stats', 'targetBreakdown'));
    }

    // ═════════════════════════════════════════════════════════════
    // EDIT
    // ═════════════════════════════════════════════════════════════
 public function edit(FinancialCampaign $campaign)
    {
        return view('owner.financial.campaigns.edit', [
            'campaign'    => $campaign,
            'categories'  => self::CATEGORIES,
            'statuses'    => self::STATUSES,
            'generations' => $this->resolveGenerations(),
            'members'     => $this->resolveMembers(),
            'debug'       => app()->hasDebugModeEnabled(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // UPDATE
    // ═════════════════════════════════════════════════════════════
    public function update(Request $request, FinancialCampaign $campaign)
    {
        $validated = $this->validateCampaign($request, $campaign);
        $validated['target_data'] = $this->buildTargetData($request);

        try {
            DB::beginTransaction();
            $campaign->update($validated);
            DB::commit();
            $this->flushSummaryCache();

            return redirect()
                ->route('owner.financial.campaigns.show', $campaign)
                ->with('success', "✅ Kampanye \"{$campaign->title}\" berhasil diperbarui.");
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[CampaignController@update] gagal', ['id' => $campaign->id, 'error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui kampanye: ' . $e->getMessage());
        }
    }

    // ═════════════════════════════════════════════════════════════
    // DESTROY
    // ═════════════════════════════════════════════════════════════
    public function destroy(FinancialCampaign $campaign)
    {
        try {
            // Soft-delete bila model mendukung
            $title = $campaign->title;
            $campaign->delete();
            $this->flushSummaryCache();

            return redirect()
                ->route('owner.financial.campaigns.index')
                ->with('success', "🗑️ Kampanye \"{$title}\" berhasil dihapus.");
        } catch (\Throwable $e) {
            Log::error('[CampaignController@destroy] gagal', ['id' => $campaign->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus kampanye: ' . $e->getMessage());
        }
    }

    // ═════════════════════════════════════════════════════════════
    // QUICK ACTIONS — aktivasi / jeda / tutup / batalkan
    // ═════════════════════════════════════════════════════════════
    public function activate(FinancialCampaign $campaign)
    {
        return $this->transition($campaign, 'active', 'diaktifkan');
    }

    public function close(FinancialCampaign $campaign)
    {
        return $this->transition($campaign, 'closed', 'ditutup');
    }

    public function cancel(FinancialCampaign $campaign)
    {
        return $this->transition($campaign, 'cancelled', 'dibatalkan');
    }

    protected function transition(FinancialCampaign $campaign, string $to, string $label)
    {
        if ($campaign->status === $to) {
            return back()->with('error', "Kampanye sudah berstatus {$label}.");
        }

        try {
            $campaign->update(['status' => $to]);
            $this->flushSummaryCache();

            return back()->with('success', "Kampanye \"{$campaign->title}\" berhasil {$label}.");
        } catch (\Throwable $e) {
            Log::error("[CampaignController@transition:{$to}] gagal", [
                'id' => $campaign->id, 'error' => $e->getMessage(),
            ]);
            return back()->with('error', "Gagal mengubah status: {$e->getMessage()}");
        }
    }

    // ═════════════════════════════════════════════════════════════
    // VALIDATION
    // ═════════════════════════════════════════════════════════════
    protected function validateCampaign(Request $request, ?FinancialCampaign $campaign = null): array
    {
        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category'    => ['required', 'string', Rule::in(self::CATEGORIES)],
            'amount'      => ['required', 'integer', 'min:0'],
            'is_required' => ['boolean'],
            'target_type' => ['required', Rule::in(['all', 'generation', 'specific'])],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['nullable', 'date', 'after:start_date'],
            'due_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'status'      => ['required', Rule::in(self::STATUSES)],
        ];

        if ($request->target_type === 'generation') {
            $rules['generation_ids']   = ['required', 'array', 'min:1'];
            $rules['generation_ids.*'] = ['exists:generations,id'];
        }

        if ($request->target_type === 'specific') {
            $rules['user_ids']   = ['required', 'array', 'min:1'];
            $rules['user_ids.*'] = ['exists:users,id'];
        }

        $messages = [
            'generation_ids.required'   => 'Pilih minimal satu generasi.',
            'generation_ids.*.exists'   => 'Generasi yang dipilih tidak valid.',
            'user_ids.required'         => 'Pilih minimal satu anggota.',
            'user_ids.*.exists'         => 'Anggota yang dipilih tidak valid.',
            'category.in'               => 'Kategori tidak valid.',
            'due_date.after_or_equal'   => 'Tanggal jatuh tempo tidak boleh sebelum tanggal mulai.',
        ];

        return $request->validate($rules, $messages);
    }

    // ═════════════════════════════════════════════════════════════
    // HELPERS
    // ═════════════════════════════════════════════════════════════

    protected function buildTargetData(Request $request): ?array
    {
        return match ($request->target_type) {
            'generation' => ['generation_ids' => array_values((array) $request->input('generation_ids', []))],
            'specific'   => ['user_ids'       => array_values((array) $request->input('user_ids', []))],
            default      => null,
        };
    }

    protected function resolveTargetBreakdown(FinancialCampaign $campaign): array
    {
        $data = (array) ($campaign->target_data ?? []);

        return [
            'type'           => $campaign->target_type ?? 'all',
            'generation_ids' => $data['generation_ids'] ?? [],
            'user_ids'       => $data['user_ids']       ?? [],
            'count'          => match ($campaign->target_type ?? 'all') {
                'generation' => count($data['generation_ids'] ?? []),
                'specific'   => count($data['user_ids'] ?? []),
                default      => null,
            },
        ];
    }

    protected function generations()
    {
        try {
            if (class_exists(\App\Models\Generation::class)) {
                return \App\Models\Generation::orderBy('name')->get(['id', 'name']);
            }
        } catch (\Throwable $e) {
            Log::warning('[CampaignController] generations() gagal', ['error' => $e->getMessage()]);
        }
        return collect();
    }

    protected function memberOptions()
    {
        try {
            return \App\Models\User::query()
                ->whereIn('level_id', [3, 4, 5])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'nim']);
        } catch (\Throwable $e) {
            Log::warning('[CampaignController] memberOptions() gagal', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    protected function hasColumn(string $column): bool
    {
        static $cache = [];
        $key = 'financial_campaigns.' . $column;

        if (!array_key_exists($key, $cache)) {
            try {
                $cache[$key] = \Schema::hasColumn('financial_campaigns', $column)
                            || \Schema::hasColumn('campaigns', $column);
            } catch (\Throwable $e) {
                $cache[$key] = false;
            }
        }

        return $cache[$key];
    }

    protected function flushSummaryCache(): void
    {
        try {
            Cache::forget('financial.campaigns.summary');
        } catch (\Throwable $e) {
            // silent — cache issue tidak boleh menggagalkan request
        }
    }
}