<?php

namespace App\Services\Financial;

use App\Models\User;
use App\Models\Financial\FeePlan;
use App\Models\Financial\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeePlanInvoiceGenerator
{
    /**
     * Preview: hitung siapa saja yang akan ditagih TANPA membuat invoice.
     *
     * @return array{
     *   plan: FeePlan,
     *   period: string,
     *   due_date: string,
     *   total_users: int,
     *   total_amount: float,
     *   users: array<int, array{id:int,name:string,nim:?string,level:string,amount:float}>
     * }
     */
    public function preview(FeePlan $plan, ?string $period = null): array
    {
        $period  = $period ?: $this->resolveCurrentPeriod($plan);
        $dueDate = $this->resolveDueDate($plan, $period);

        $users = $this->resolveEligibleUsers($plan);

        // Skip user yang sudah punya invoice plan ini di periode sama
        $alreadyInvoiced = Invoice::query()
            ->where('fee_plan_id', $plan->id)
            ->where('period', $period)
            ->pluck('user_id')
            ->all();

        $users = $users->reject(fn (User $u) => in_array($u->id, $alreadyInvoiced));

        $rows = [];
        $total = 0;

        foreach ($users as $user) {
            $amount = $this->resolveAmountForUser($plan, $user);
            if ($amount <= 0) continue;

            $rows[] = [
                'id'     => $user->id,
                'name'   => $user->name,
                'nim'    => $user->nim,
                'level'  => $user->level->name ?? '-',
                'amount' => $amount,
            ];
            $total += $amount;
        }

        return [
            'plan'         => $plan,
            'period'       => $period,
            'due_date'     => $dueDate->toDateString(),
            'total_users'  => count($rows),
            'total_amount' => $total,
            'users'        => $rows,
            'skipped'      => count($alreadyInvoiced),
        ];
    }

    /**
     * Generate invoice riil untuk semua user yang eligible.
     *
     * @return array{created:int,skipped:int,total_amount:float,period:string}
     */
    public function generate(FeePlan $plan, ?string $period = null): array
    {
        $preview = $this->preview($plan, $period);

        if ($preview['total_users'] === 0) {
            return [
                'created'      => 0,
                'skipped'      => $preview['skipped'],
                'total_amount' => 0,
                'period'       => $preview['period'],
            ];
        }

        $created = 0;
        $total   = 0;

        DB::transaction(function () use ($plan, $preview, &$created, &$total) {
            foreach ($preview['users'] as $row) {
                $subtotal = (float) $row['amount'];
                $penalty  = 0;
                $discount = 0;
                $grandTotal = $subtotal + $penalty - $discount;

                Invoice::create([
                    'invoice_number'      => $this->generateInvoiceNumber($plan),
                    'user_id'             => $row['id'],
                    'generation_id'       => $plan->generation_id,
                    'fee_plan_id'         => $plan->id,
                    'financial_campaign_id' => null,
                    'type'                => $plan->fee_type,
                    'period'              => $preview['period'],
                    'standard_due_date'   => $preview['due_date'],
                    'adjusted_due_date'   => null,
                    'final_due_date'      => $preview['due_date'],
                    'due_date_reason'     => 'Auto-generated dari skema: ' . $plan->name,
                    'subtotal'            => $subtotal,
                    'penalty'             => $penalty,
                    'discount'            => $discount,
                    'total'               => $grandTotal,
                    'status'              => 'pending',
                    'meta'                => [
                        'fee_plan_code' => $plan->code,
                        'fee_plan_name' => $plan->name,
                        'frequency'     => $plan->frequency,
                        'generated_by'  => auth()->id(),
                        'generated_at'  => now()->toIso8601String(),
                    ],
                ]);

                $created++;
                $total += $grandTotal;
            }
        });

        return [
            'created'      => $created,
            'skipped'      => $preview['skipped'],
            'total_amount' => $total,
            'period'       => $preview['period'],
        ];
    }

    /**
     * Tentukan user mana saja yang eligible untuk plan ini.
     * Menggunakan target_type + target_data + membership_rule.
     */
    protected function resolveEligibleUsers(FeePlan $plan)
    {
        $query = User::query()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->with(['level', 'jabatans']);

        switch ($plan->target_type) {
            case 'all_members':
                // Semua anggota UKM + pengurus
                $query->whereIn('level_id', [3, 4, 5]);
                break;

            case 'general_members':
                $query->where('level_id', 5);
                break;

            case 'officials':
                $query->whereIn('level_id', [3, 4]);
                break;

            case 'core_officials':
                $query->whereIn('level_id', [1, 2, 3]);
                break;

            case 'group':
                if (!empty($plan->target_data['group_ids'])) {
                    $groupIds = $plan->target_data['group_ids'];
                    $query->whereHas('groups', function ($q) use ($groupIds) {
                        $q->whereIn('group_id', $groupIds);
                    });
                } else {
                    return collect();
                }
                break;

            case 'individual':
                if (!empty($plan->target_data['user_ids'])) {
                    $query->whereIn('id', $plan->target_data['user_ids']);
                } else {
                    return collect();
                }
                break;

            default:
                return collect();
        }

        // Filter tambahan: generation_id (kalau ada)
        if ($plan->generation_id) {
            // Asumsi: user punya relasi generation via kolom generation_id
            if (\Schema::hasColumn('users', 'generation_id')) {
                $query->where('generation_id', $plan->generation_id);
            }
        }

        $users = $query->get();

        // Filter dengan membership_rule dari FeePlan
        return $users->filter(fn (User $u) => $plan->shouldGenerateInvoiceForPeriod(
            $u,
            $this->resolveCurrentPeriod($plan)
        ));
    }

    /**
     * Hitung nominal per user berdasarkan nominal_type.
     */
    protected function resolveAmountForUser(FeePlan $plan, User $user): float
    {
        if ($plan->nominal_type === 'fixed') {
            return (float) $plan->amount;
        }

        if ($plan->nominal_type === 'by_position' && is_array($plan->nominal_data)) {
            $jabatan = $user->jabatans()->first();
            if ($jabatan && isset($plan->nominal_data[$jabatan->id])) {
                return (float) $plan->nominal_data[$jabatan->id];
            }
        }

        if ($plan->nominal_type === 'by_member' && is_array($plan->nominal_data)) {
            if (isset($plan->nominal_data[$user->id])) {
                return (float) $plan->nominal_data[$user->id];
            }
        }

        return (float) $plan->amount;
    }

    /**
     * Period string untuk invoice (contoh: "2026-10").
     */
    protected function resolveCurrentPeriod(FeePlan $plan): string
    {
        if ($plan->frequency === 'one_time') {
            return 'ONE-TIME-' . now()->format('Ymd');
        }
        return now()->format('Y-m');
    }

    /**
     * Hitung tanggal jatuh tempo berdasarkan due_day + non_active_day_policy.
     */
    protected function resolveDueDate(FeePlan $plan, string $period): Carbon
    {
        $dueDay = (int) ($plan->due_day ?: 10);
        $baseDate = Carbon::createFromFormat('Y-m', substr($period, 0, 7))
            ->startOfMonth()
            ->addDays($dueDay - 1);

        // Handle non-active day
        if ($plan->non_active_day_policy === 'next_active') {
            while ($baseDate->isWeekend()) $baseDate->addDay();
        } elseif ($plan->non_active_day_policy === 'prev_active') {
            while ($baseDate->isWeekend()) $baseDate->subDay();
        }

        return $baseDate;
    }

    /**
     * Nomor invoice unik: INV-YYYYMMDD-XXXX.
     */
    protected function generateInvoiceNumber(FeePlan $plan): string
    {
        do {
            $number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Invoice::where('invoice_number', $number)->exists());

        return $number;
    }
}