<?php

namespace App\Services\Financial;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MemberFinancialService
{
    /**
     * Kandidat model invoice.
     */
    protected array $invoiceModels = [
        \App\Models\Financial\Invoice::class,
        \App\Models\Invoice::class,
        \App\Models\Financial\FinancialInvoice::class,
        \App\Models\Tagihan::class,
    ];

    /**
     * Kandidat model payment.
     */
    protected array $paymentModels = [
        \App\Models\Financial\Payment::class,
        \App\Models\Financial\PaymentTransaction::class,
        \App\Models\Payment::class,
        \App\Models\PaymentTransaction::class,
        \App\Models\Financial\Transaction::class,
    ];

    /**
     * Kandidat model campaign.
     */
    protected array $campaignModels = [
        \App\Models\Financial\FinancialCampaign::class,
        \App\Models\Financial\Campaign::class,
        \App\Models\Campaign::class,
    ];

    // ═══════════════════════════════════════════════════════════
    // PUBLIC API — PER USER
    // ═══════════════════════════════════════════════════════════

    /**
     * Ringkasan keuangan lengkap untuk user tertentu.
     */
    public function summaryFor(User $user): array
    {
        return [
            'user'             => $user,
            'invoices'         => $this->userInvoiceSummary($user),
            'payments'         => $this->userPaymentSummary($user),
            'campaigns'        => $this->userCampaignSummary($user),
            'recent_payments'  => $this->userRecentPayments($user, 5),
            'overdue_invoices' => $this->userOverdueInvoices($user, 5),
            'upcoming_invoices'=> $this->userUpcomingInvoices($user, 5),
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // PUBLIC API — GLOBAL (untuk ketua/pengurus overview)
    // ═══════════════════════════════════════════════════════════

    public function globalSummary(): array
    {
        return [
            'overview'         => $this->globalOverview(),
            'recent_payments'  => $this->globalRecentPayments(8),
            'top_debtors'      => $this->globalTopDebtors(5),
            'active_campaigns' => $this->globalActiveCampaigns(5),
        ];
    }

    // ═══════════════════════════════════════════════════════════
    // USER-LEVEL SUMMARIES
    // ═══════════════════════════════════════════════════════════

    protected function userInvoiceSummary(User $user): array
    {
        $model = $this->resolveModel($this->invoiceModels);
        if (!$model) {
            return $this->emptyInvoiceSummary();
        }

        try {
            $base = $model::query()->where('user_id', $user->id);

            $total    = (clone $base)->count();
            $paid     = (clone $base)->whereIn('status', ['paid', 'settled', 'success'])->count();
            $pending  = (clone $base)->where('status', 'pending')->count();
            $overdue  = (clone $base)->where('status', 'overdue')->count();
            $partial  = (clone $base)->where('status', 'partial')->count();

            return [
                'total'        => $total,
                'paid'         => $paid,
                'pending'      => $pending,
                'overdue'      => $overdue,
                'partial'      => $partial,
                'total_amount' => (float) (clone $base)->sum('total'),
                'paid_amount'  => (float) (clone $base)->whereIn('status', ['paid', 'settled', 'success'])->sum('total'),
                'outstanding'  => (float) (clone $base)->whereNotIn('status', ['paid', 'settled', 'success', 'cancelled'])->sum('total'),
            ];
        } catch (\Throwable $e) {
            Log::warning('[MemberFinancialService] userInvoiceSummary gagal', ['error' => $e->getMessage()]);
            return $this->emptyInvoiceSummary();
        }
    }

    protected function userPaymentSummary(User $user): array
    {
        $model = $this->resolveModel($this->paymentModels);
        if (!$model) return $this->emptyPaymentSummary();

        try {
            $base = $model::query()->where('user_id', $user->id);

            return [
                'total'         => (clone $base)->count(),
                'completed'     => (clone $base)->whereIn('status', ['paid', 'settled', 'success'])->count(),
                'pending'       => (clone $base)->where('status', 'pending')->count(),
                'failed'        => (clone $base)->where('status', 'failed')->count(),
                'total_paid'    => (float) (clone $base)->whereIn('status', ['paid', 'settled', 'success'])->sum('amount'),
                'this_month'    => (float) (clone $base)
                    ->whereIn('status', ['paid', 'settled', 'success'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount'),
            ];
        } catch (\Throwable $e) {
            Log::warning('[MemberFinancialService] userPaymentSummary gagal', ['error' => $e->getMessage()]);
            return $this->emptyPaymentSummary();
        }
    }

    protected function userCampaignSummary(User $user): array
    {
        $model = $this->resolveModel($this->campaignModels);
        if (!$model) return ['total' => 0, 'active' => 0, 'items' => collect()];

        try {
            $items = $model::query()
                ->whereIn('status', ['active', 'published'])
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            return [
                'total'  => $items->count(),
                'active' => $items->count(),
                'items'  => $items,
            ];
        } catch (\Throwable $e) {
            Log::warning('[MemberFinancialService] userCampaignSummary gagal', ['error' => $e->getMessage()]);
            return ['total' => 0, 'active' => 0, 'items' => collect()];
        }
    }

    protected function userRecentPayments(User $user, int $limit = 5): Collection
    {
        $model = $this->resolveModel($this->paymentModels);
        if (!$model) return collect();

        try {
            return $model::query()
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    protected function userOverdueInvoices(User $user, int $limit = 5): Collection
    {
        $model = $this->resolveModel($this->invoiceModels);
        if (!$model) return collect();

        try {
            $q = $model::query()
                ->where('user_id', $user->id)
                ->whereNotIn('status', ['paid', 'settled', 'success', 'cancelled']);

            if (Schema::hasColumn($model::make()->getTable(), 'due_date')) {
                $q->where(function ($w) {
                    $w->where('status', 'overdue')
                      ->orWhereDate('due_date', '<', now()->toDateString());
                });
            }

            return $q->orderBy('due_date')->limit($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    protected function userUpcomingInvoices(User $user, int $limit = 5): Collection
    {
        $model = $this->resolveModel($this->invoiceModels);
        if (!$model) return collect();

        try {
            $q = $model::query()
                ->where('user_id', $user->id)
                ->whereNotIn('status', ['paid', 'settled', 'success', 'cancelled']);

            $table = $model::make()->getTable();
            if (Schema::hasColumn($table, 'due_date')) {
                $q->whereDate('due_date', '>=', now()->toDateString())
                  ->orderBy('due_date');
            } else {
                $q->orderByDesc('created_at');
            }

            return $q->limit($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    // ═══════════════════════════════════════════════════════════
    // GLOBAL SUMMARIES
    // ═══════════════════════════════════════════════════════════

    protected function globalOverview(): array
    {
        $invoice = $this->resolveModel($this->invoiceModels);
        $payment = $this->resolveModel($this->paymentModels);

        $result = [
            'total_invoices'    => 0,
            'total_paid'        => 0.0,
            'total_outstanding' => 0.0,
            'overdue_count'     => 0,
            'paid_this_month'   => 0.0,
            'active_campaigns'  => 0,
        ];

        try {
            if ($invoice) {
                $result['total_invoices']    = (int) $invoice::count();
                $result['total_paid']        = (float) $invoice::whereIn('status', ['paid', 'settled', 'success'])->sum('total');
                $result['total_outstanding'] = (float) $invoice::whereNotIn('status', ['paid', 'settled', 'success', 'cancelled'])->sum('total');
                $result['overdue_count']     = (int) $invoice::where('status', 'overdue')->count();
            }

            if ($payment) {
                $result['paid_this_month'] = (float) $payment::whereIn('status', ['paid', 'settled', 'success'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
            }

            $campaign = $this->resolveModel($this->campaignModels);
            if ($campaign) {
                $result['active_campaigns'] = (int) $campaign::where('status', 'active')->count();
            }
        } catch (\Throwable $e) {
            Log::warning('[MemberFinancialService] globalOverview gagal', ['error' => $e->getMessage()]);
        }

        return $result;
    }

    protected function globalRecentPayments(int $limit = 8): Collection
    {
        $model = $this->resolveModel($this->paymentModels);
        if (!$model) return collect();

        try {
            return $model::query()
                ->with(['user:id,name,nim'])
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    protected function globalTopDebtors(int $limit = 5): Collection
    {
        $model = $this->resolveModel($this->invoiceModels);
        if (!$model) return collect();

        try {
            return $model::query()
                ->select('user_id', DB::raw('SUM(total) as total_debt'), DB::raw('COUNT(*) as count'))
                ->whereNotIn('status', ['paid', 'settled', 'success', 'cancelled'])
                ->groupBy('user_id')
                ->orderByDesc('total_debt')
                ->with(['user:id,name,nim'])
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    protected function globalActiveCampaigns(int $limit = 5): Collection
    {
        $model = $this->resolveModel($this->campaignModels);
        if (!$model) return collect();

        try {
            return $model::query()
                ->where('status', 'active')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    // ═══════════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════════

    protected function resolveModel(array $candidates): ?string
    {
        foreach ($candidates as $class) {
            if (class_exists($class)) return $class;
        }
        return null;
    }

    protected function emptyInvoiceSummary(): array
    {
        return [
            'total' => 0, 'paid' => 0, 'pending' => 0,
            'overdue' => 0, 'partial' => 0,
            'total_amount' => 0.0, 'paid_amount' => 0.0, 'outstanding' => 0.0,
        ];
    }

    protected function emptyPaymentSummary(): array
    {
        return [
            'total' => 0, 'completed' => 0, 'pending' => 0,
            'failed' => 0, 'total_paid' => 0.0, 'this_month' => 0.0,
        ];
    }
}