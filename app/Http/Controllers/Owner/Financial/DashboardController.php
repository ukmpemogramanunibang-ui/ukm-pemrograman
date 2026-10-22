<?php

namespace App\Http\Controllers\Owner\Financial;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Financial\FeePlan;
use App\Models\Financial\Invoice;
use App\Models\Financial\PaymentTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $orgId = \App\Services\OrganizationContext::id();

        // ========== STATISTIK INVOICE (Otomatis terfilter Global Scope) ==========
        $totalInvoices   = Invoice::count();
        $pendingInvoices = Invoice::where('status', 'pending')->count();
        $paidInvoices    = Invoice::where('status', 'paid')->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        $totalPayments = PaymentTransaction::sum('amount') ?? 0;
        $totalPenalty  = Invoice::sum('penalty') ?? 0;

        // ========== STATISTIK ANGGOTA (Spesifik Organisasi) ==========
        // Hitung dari OrganizationMembership, bukan dari tabel users global
        $totalMembers = \App\Models\OrganizationMembership::where('organization_id', $orgId)
            ->where('membership_status', 'active')
            ->count();

        $totalOfficials = \App\Models\OrganizationMembership::where('organization_id', $orgId)
            ->where('access_role', 'officer')
            ->where('membership_status', 'active')
            ->count();

        // ========== STATISTIK FEE PLAN (Otomatis terfilter Global Scope) ==========
        try {
            $totalFeePlans = FeePlan::count();

            $totalActiveFeePlans = FeePlan::query()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('effective_from')->orWhere('effective_from', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('effective_until')->orWhere('effective_until', '>=', now());
                })
                ->count();

            $activeFeePlans = FeePlan::query()
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('effective_from')->orWhere('effective_from', '<=', now());
                })
                ->where(function ($q) {
                    $q->whereNull('effective_until')->orWhere('effective_until', '>=', now());
                })
                ->with('generation')
                ->orderByDesc('effective_from')
                ->limit(6)
                ->get();

            $estimatedMonthlyIncome = $activeFeePlans->sum(function ($plan) {
                $targetCount = $this->countTargetUsers($plan);
                return (float) $plan->amount * $targetCount;
            });

            $today = now()->day;
            $dueThisWeek = FeePlan::query()
                ->where('status', 'active')
                ->whereBetween('due_day', [$today, min($today + 7, 28)])
                ->orderBy('due_day')
                ->get();
        } catch (\Throwable $e) {
            \Log::warning('[Financial Dashboard] Fee plan stats gagal: ' . $e->getMessage());
            $totalFeePlans          = 0;
            $totalActiveFeePlans    = 0;
            $activeFeePlans         = collect();
            $estimatedMonthlyIncome = 0;
            $dueThisWeek            = collect();
        }

        // ========== DATA BULANAN ==========
        $monthlyData = Invoice::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total) as total')
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        return view('owner.financial.dashboard', compact(
            'totalInvoices', 'pendingInvoices', 'paidInvoices', 'overdueInvoices',
            'totalPayments', 'totalPenalty', 'totalMembers', 'totalOfficials',
            'monthlyData', 'totalFeePlans', 'activeFeePlans', 
            'totalActiveFeePlans', 'estimatedMonthlyIncome', 'dueThisWeek'
        ));
    }

    /**
     * Hitung jumlah user yang cocok dengan target_type plan.
     */
    protected function countTargetUsers(FeePlan $plan): int
    {
        try {
            $query = User::query()->whereNull('deleted_at')->where('is_active', true);

            return match ($plan->target_type) {
                'all_members'     => $query->whereIn('level_id', [3, 4, 5])->count(),
                'general_members' => $query->where('level_id', 5)->count(),
                'officials'       => $query->whereIn('level_id', [3, 4])->count(),
                'core_officials'  => $query->whereIn('level_id', [1, 2, 3])->count(),
                'group'           => isset($plan->target_data['group_ids'])
                                        ? $query->whereHas('groups', fn ($q) => $q->whereIn('group_id', $plan->target_data['group_ids']))->count()
                                        : 0,
                'individual'      => isset($plan->target_data['user_ids'])
                                        ? count($plan->target_data['user_ids'])
                                        : 0,
                default           => 0,
            };
        } catch (\Throwable $e) {
            return 0;
        }
    }
}