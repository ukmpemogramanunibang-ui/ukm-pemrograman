<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\Generation;
use App\Models\Financial\FeePlan;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeePlanController extends Controller
{
    public function index(Request $request)
    {
        $query = FeePlan::with(['generation', 'creator', 'approver'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by fee type
        if ($request->has('fee_type') && $request->fee_type !== 'all') {
            $query->where('fee_type', $request->fee_type);
        }

        // Filter by generation
        if ($request->has('generation_id') && $request->generation_id) {
            $query->where('generation_id', $request->generation_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $feePlans = $query->paginate(15)->withQueryString();

        $generations = Generation::where('is_active', true)->get();

        return view('owner.financial.fee-plans.index', compact('feePlans', 'generations'));
    }

    public function create()
    {
        $generations = Generation::where('is_active', true)->get();
        $users = User::whereIn('level_id', [1, 2, 3, 4, 5])->orderBy('name')->get();
        $groups = Group::orderBy('name')->get();

        // Data untuk escalation rules default
        $defaultEscalation = [
            ['level' => 1, 'late_count' => 1, 'actions' => ['warning']],
            ['level' => 2, 'late_count' => 2, 'actions' => ['warning', 'penalty'], 'penalty_amount' => 1000],
            ['level' => 3, 'late_count' => 3, 'actions' => ['warning', 'penalty', 'task'], 'penalty_amount' => 2500],
            ['level' => 4, 'late_count' => 4, 'actions' => ['warning', 'review', 'evaluation']],
        ];

        return view('owner.financial.fee-plans.create', compact(
            'generations', 'users', 'groups', 'defaultEscalation'
        ));
    }

    public function store(Request $request)
{
    $validated = $this->validateRequest($request);
\Log::info('Data yang akan disimpan:', $validated);
    DB::beginTransaction();
    try {
        $validated['created_by'] = auth()->id();
        $validated['code'] = $this->generateCode();

        // Set period_type = frequency (jika tidak ada di form)
        if (empty($validated['period_type'])) {
            $validated['period_type'] = $validated['frequency'] ?? 'monthly';
        }

        // Proses target_data
        $validated['target_data'] = $this->processTargetData($request);
$validated['generation_id'] = $request->input('generation_id');
        // Proses nominal_data
        $validated['nominal_data'] = $this->processNominalData($request);

        // Proses penalty escalation
        if ($request->has('penalty_escalation')) {
            $validated['penalty_escalation'] = $request->input('penalty_escalation');
        }

        // Proses membership rule
        $validated['membership_rule'] = $this->processMembershipRule($request);

        // Proses calendar sources
        $validated['calendar_sources'] = $this->processCalendarSources($request);

        // Proses sanction rules
        if ($request->has('sanction_rules')) {
            $validated['sanction_rules'] = $request->input('sanction_rules');
        }

        // Set default values untuk field opsional
        $validated['non_active_day_policy'] = $request->input('non_active_day_policy', 'next_active');
        $validated['grace_period'] = $request->input('grace_period', 3);
        $validated['allow_partial'] = $request->boolean('allow_partial');
        $validated['allow_exemption'] = $request->boolean('allow_exemption');
        $validated['penalty_enabled'] = $request->boolean('penalty_enabled');
        $validated['exemption_require_approval'] = $request->boolean('exemption_require_approval', true);
        $validated['exemption_reason_required'] = $request->boolean('exemption_reason_required', true);
        $validated['min_partial_amount'] = $request->input('min_partial_amount');
        $validated['max_installments'] = $request->input('max_installments');
        $validated['status'] = $request->input('status', 'active');

        // **Tambahkan validasi tambahan untuk field yang wajib**
        if (empty($validated['effective_from'])) {
            throw new \Exception('Tanggal berlaku mulai wajib diisi.');
        }

        $feePlan = FeePlan::create($validated);

        DB::commit();

        return redirect()->route('owner.financial.fee-plans.index')
            ->with('success', '✅ Skema biaya "' . $feePlan->name . '" berhasil dibuat.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        // Log error untuk debugging
        \Log::error('Gagal menyimpan skema biaya: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return back()->with('error', '❌ Gagal menyimpan skema: ' . $e->getMessage())->withInput();
    }
}

// Helper methods (pindahkan dari store agar rapi)
protected function processTargetData(Request $request): ?array
{
    if ($request->target_type === 'group' && $request->has('target_data.group_ids')) {
        return ['group_ids' => $request->input('target_data.group_ids')];
    }
    if ($request->target_type === 'individual' && $request->has('target_data.user_ids')) {
        return ['user_ids' => $request->input('target_data.user_ids')];
    }
    return null;
}

protected function processNominalData(Request $request): ?array
{
    if (in_array($request->nominal_type, ['by_position', 'by_member']) && $request->has('nominal_data')) {
        return $request->input('nominal_data');
    }
    return null;
}

protected function processMembershipRule(Request $request): ?array
{
    $rule = [];
    if ($request->boolean('membership_rule.require_active_membership')) {
        $rule['require_active_membership'] = true;
    }
    if ($request->has('membership_rule.start_billing')) {
        $rule['start_billing'] = $request->input('membership_rule.start_billing');
    }
    if ($request->has('membership_rule.cutoff_day')) {
        $rule['cutoff_day'] = (int) $request->input('membership_rule.cutoff_day');
    }
    return !empty($rule) ? $rule : null;
}

protected function processCalendarSources(Request $request): ?array
{
    $sources = [];
    if ($request->boolean('calendar_sources.national_holidays')) {
        $sources['national_holidays'] = true;
    }
    if ($request->boolean('calendar_sources.weekends')) {
        $sources['weekends'] = true;
    }
    if ($request->boolean('calendar_sources.academic_calendar')) {
        $sources['academic_calendar'] = true;
    }
    if ($request->boolean('calendar_sources.ukm_activities')) {
        $sources['ukm_activities'] = true;
    }
    return !empty($sources) ? $sources : null;
}

    public function show(FeePlan $feePlan)
    {
        $feePlan->load(['generation', 'creator', 'updater', 'approver', 'invoices' => function ($q) {
            $q->limit(20);
        }]);

        // Statistik
        $totalInvoices = $feePlan->invoices()->count();
        $paidInvoices = $feePlan->invoices()->where('status', 'paid')->count();
        $pendingInvoices = $feePlan->invoices()->where('status', 'pending')->count();
        $overdueInvoices = $feePlan->invoices()->where('status', 'overdue')->count();
        $totalRevenue = $feePlan->invoices()->where('status', 'paid')->sum('total');

        return view('owner.financial.fee-plans.show', compact(
            'feePlan', 'totalInvoices', 'paidInvoices', 'pendingInvoices',
            'overdueInvoices', 'totalRevenue'
        ));
    }

    public function edit(FeePlan $feePlan)
    {
        $generations = Generation::where('is_active', true)->get();
        $users = User::whereIn('level_id', [1, 2, 3, 4, 5])->orderBy('name')->get();
        $groups = Group::orderBy('name')->get();

        return view('owner.financial.fee-plans.edit', compact('feePlan', 'generations', 'users', 'groups'));
    }

    public function update(Request $request, FeePlan $feePlan)
    {
        $validated = $this->validateRequest($request, $feePlan);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = auth()->id();

            // Jika status diubah menjadi superseded, set superseded_at
            if (isset($validated['status']) && $validated['status'] === 'superseded') {
                $validated['superseded_at'] = now();
            }

            // Proses target_data (sama seperti store)
            if ($request->target_type === 'group' && $request->has('target_data.group_ids')) {
                $validated['target_data'] = ['group_ids' => $request->input('target_data.group_ids')];
            } elseif ($request->target_type === 'individual' && $request->has('target_data.user_ids')) {
                $validated['target_data'] = ['user_ids' => $request->input('target_data.user_ids')];
            } else {
                $validated['target_data'] = null;
            }

            // Proses nominal_data
            if ($request->nominal_type === 'by_position' && $request->has('nominal_data')) {
                $validated['nominal_data'] = $request->input('nominal_data');
            } elseif ($request->nominal_type === 'by_member' && $request->has('nominal_data')) {
                $validated['nominal_data'] = $request->input('nominal_data');
            } else {
                $validated['nominal_data'] = null;
            }

            // Proses penalty escalation
            if ($request->has('penalty_escalation')) {
                $validated['penalty_escalation'] = $request->input('penalty_escalation');
            }

            // Proses membership rule
            $membershipRule = [];
            if ($request->has('membership_rule.require_active_membership')) {
                $membershipRule['require_active_membership'] = true;
            }
            if ($request->has('membership_rule.start_billing')) {
                $membershipRule['start_billing'] = $request->input('membership_rule.start_billing');
            }
            if ($request->has('membership_rule.cutoff_day')) {
                $membershipRule['cutoff_day'] = (int) $request->input('membership_rule.cutoff_day');
            }
            $validated['membership_rule'] = !empty($membershipRule) ? $membershipRule : null;

            // Proses sanction rules
            if ($request->has('sanction_rules')) {
                $validated['sanction_rules'] = $request->input('sanction_rules');
            }

            // Proses calendar sources
            $calendarSources = [];
            if ($request->has('calendar_sources.national_holidays')) {
                $calendarSources['national_holidays'] = true;
            }
            if ($request->has('calendar_sources.weekends')) {
                $calendarSources['weekends'] = true;
            }
            if ($request->has('calendar_sources.academic_calendar')) {
                $calendarSources['academic_calendar'] = true;
            }
            if ($request->has('calendar_sources.ukm_activities')) {
                $calendarSources['ukm_activities'] = true;
            }
            $validated['calendar_sources'] = !empty($calendarSources) ? $calendarSources : null;

            $feePlan->update($validated);

            DB::commit();

            return redirect()->route('owner.financial.fee-plans.index')
                ->with('success', 'Skema biaya "' . $feePlan->name . '" berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(FeePlan $feePlan)
    {
        if ($feePlan->invoices()->count() > 0) {
            return back()->with('error', 'Skema biaya tidak dapat dihapus karena sudah digunakan dalam tagihan.');
        }

        $feePlan->delete();
        return back()->with('success', 'Skema biaya berhasil dihapus.');
    }

    public function duplicate(FeePlan $feePlan)
    {
        $newFeePlan = $feePlan->replicate();
        $newFeePlan->name = $feePlan->name . ' (Copy)';
        $newFeePlan->code = $this->generateCode();
        $newFeePlan->status = 'inactive';
        $newFeePlan->created_by = auth()->id();
        $newFeePlan->save();

        return redirect()->route('owner.financial.fee-plans.edit', $newFeePlan)
            ->with('success', 'Skema biaya berhasil diduplikasi. Silakan edit sesuai kebutuhan.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        // Import logic for historical data
        // This will be implemented later

        return redirect()->route('owner.financial.fee-plans.index')
            ->with('info', 'Fitur import historis sedang dalam pengembangan.');
    }

    // ===== VALIDASI =====
    protected function validateRequest(Request $request, $feePlan = null)
    {
        $rules = [
            'generation_id' => 'required|exists:generations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fee_type' => 'required|in:monthly_cash,registration,special_contribution,penalty,activity,other',
            'target_type' => 'required|in:all_members,general_members,officials,core_officials,group,individual',
            'target_data' => 'nullable|array',
            'amount' => 'required|numeric|min:0',
            'nominal_type' => 'required|in:fixed,by_position,by_member',
            'nominal_data' => 'nullable|array',
            'frequency' => 'required|in:monthly,biweekly,weekly,one_time',
            'period_type' => 'nullable|in:monthly,biweekly,weekly,one_time',
            'due_day' => 'required|integer|min:1|max:31',
            'grace_period' => 'nullable|integer|min:0|max:60',
            'non_active_day_policy' => 'required|in:fixed,next_active,prev_active',
            'calendar_sources' => 'nullable|array',
            'penalty_enabled' => 'boolean',
            'penalty_type' => 'nullable|in:fixed,percentage|required_if:penalty_enabled,true',
            'penalty_amount' => 'nullable|numeric|min:0|required_if:penalty_enabled,true',
            'penalty_escalation' => 'nullable|array',
            'allow_partial' => 'boolean',
            'min_partial_amount' => 'nullable|numeric|min:0|required_if:allow_partial,true',
            'max_installments' => 'nullable|integer|min:1|max:12|required_if:allow_partial,true',
            'allow_exemption' => 'boolean',
            'exemption_require_approval' => 'boolean',
            'exemption_reason_required' => 'boolean',
            'effective_from' => 'required|date',
            'effective_until' => 'nullable|date|after:effective_from',
            'start_period' => 'nullable|date',
            'end_period' => 'nullable|date|after:start_period',
            'membership_rule' => 'nullable|array',
            'sanction_rules' => 'nullable|array',
            'status' => 'required|in:active,inactive,archived,superseded',
            'notes' => 'nullable|string',
        ];

        // Validasi target_data
        if ($request->target_type === 'group') {
            $rules['target_data.group_ids'] = 'required|array|min:1';
            $rules['target_data.group_ids.*'] = 'exists:groups,id';
        }

        if ($request->target_type === 'individual') {
            $rules['target_data.user_ids'] = 'required|array|min:1';
            $rules['target_data.user_ids.*'] = 'exists:users,id';
        }

        // Validasi nominal_data
        if ($request->nominal_type === 'by_position') {
            $rules['nominal_data'] = 'required|array|min:1';
        }

        if ($request->nominal_type === 'by_member') {
            $rules['nominal_data'] = 'required|array|min:1';
        }

        $messages = [
            'target_data.group_ids.required' => 'Pilih minimal satu kelompok.',
            'target_data.user_ids.required' => 'Pilih minimal satu anggota.',
            'nominal_data.required' => 'Data nominal harus diisi.',
            'penalty_type.required_if' => 'Jenis denda wajib diisi jika denda diaktifkan.',
            'penalty_amount.required_if' => 'Nominal denda wajib diisi jika denda diaktifkan.',
            'min_partial_amount.required_if' => 'Minimal cicilan wajib diisi jika cicilan diaktifkan.',
            'max_installments.required_if' => 'Maksimal cicilan wajib diisi jika cicilan diaktifkan.',
        ];

        return $request->validate($rules, $messages);
    }

    protected function generateCode(): string
    {
        $prefix = 'SKEMA';
        $number = FeePlan::count() + 1;
        return $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}