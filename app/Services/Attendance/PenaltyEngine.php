<?php

namespace App\Services\Attendance;

use App\Models\AttendancePenalty;
use App\Models\AttendancePenaltyAuditLog;
use App\Models\AttendancePenaltyRule;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PenaltyEngine
{
    public function __construct(
        protected ?NotificationDispatcher $notifier = null
    ) {
        $this->notifier ??= new NotificationDispatcher();
    }

    // =========================================================
    // EVALUATE
    // =========================================================
    public function evaluate(AttendanceRecord $record): ?AttendancePenalty
    {
        $violation = $this->mapViolation($record);
        if (!$violation) return null;

        // Skip jika sudah pernah generate untuk record ini
        $existing = AttendancePenalty::where('attendance_record_id', $record->id)->first();
        if ($existing) return $existing;

        $occurrences = $this->countOccurrences($record, $violation);

        $rule = $this->resolveRule($violation, $occurrences);
        if (!$rule || !$rule->auto_generate) return null;

        return DB::transaction(function () use ($record, $rule, $violation, $occurrences) {
            $penalty = AttendancePenalty::create([
                'case_number'           => AttendancePenalty::generateCaseNumber(),
                'user_id'               => $record->user_id,
                'group_id'              => $record->group_id ?? null,
                'attendance_session_id' => $record->attendance_session_id ?? null,
                'attendance_record_id'  => $record->id,
                'rule_id'               => $rule->id,
                'penalty_code'          => $rule->code,
                'penalty_type'          => $rule->penalty_type,
                'violation_type'        => $violation,
                'severity'              => $rule->severity,
                'points'                => $rule->point_penalty,
                'amount'                => $rule->financial_amount,
                'currency'              => $rule->currency ?? 'IDR',
                'warning_level'         => $rule->warning_level,
                'status'                => $rule->requires_review ? 'review' : ($rule->requires_approval ? 'pending' : 'active'),
                'payment_status'        => $rule->financial_amount > 0 ? 'unpaid' : 'paid',
                'reason'                => $this->buildReason($record, $violation, $occurrences),
                'issued_at'             => now(),
                'due_at'                => $rule->due_days > 0 ? now()->addDays($rule->due_days) : null,
                'created_by'            => Auth::id(),
                'meta'                  => [
                    'rule_code'    => $rule->code,
                    'occurrences'  => $occurrences,
                    'period_scope' => $rule->period_scope,
                ],
            ]);

            $this->audit($penalty, 'created', "Denda dibuat otomatis oleh Rule Engine ({$rule->code}).");

            // Jalankan escalation
            $this->runEscalation($penalty, $rule);

            // Kirim notifikasi
            $this->notifier->penaltyCreated($penalty);

            return $penalty;
        });
    }

    // =========================================================
    // ACTIONS
    // =========================================================
    public function approve(AttendancePenalty $p, ?int $byUserId = null): void
    {
        $this->transition($p, [
            'status'      => 'active',
            'approved_by' => $byUserId ?? Auth::id(),
            'approved_at' => now(),
        ], 'approved', 'Denda disetujui.');
    }

    public function reject(AttendancePenalty $p, string $reason): void
    {
        $this->transition($p, [
            'status'              => 'cancelled',
            'cancelled_by'        => Auth::id(),
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason,
        ], 'rejected', "Ditolak: {$reason}");
    }

    public function cancel(AttendancePenalty $p, string $reason): void
    {
        $this->transition($p, [
            'status'              => 'cancelled',
            'cancelled_by'        => Auth::id(),
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason,
        ], 'cancelled', "Dibatalkan: {$reason}");
        $this->notifier->penaltyCancelled($p);
    }

    public function waive(AttendancePenalty $p, string $reason): void
    {
        $this->transition($p, [
            'status'              => 'waived',
            'cancellation_reason' => $reason,
            'resolved_at'         => now(),
        ], 'waived', "Dibebaskan: {$reason}");
        $this->notifier->penaltyWaived($p);
    }

    public function reverse(AttendancePenalty $p, string $reason): void
    {
        $this->transition($p, [
            'status'              => 'reversed',
            'cancellation_reason' => $reason,
            'resolved_at'         => now(),
        ], 'reversed', "Reversal: {$reason}");
    }

    public function pay(AttendancePenalty $p, float $amount, ?string $method = null): void
    {
        $paid          = min((float) $p->amount, (float) $p->amount_paid + $amount);
        $paymentStatus = $paid >= (float) $p->amount ? 'paid' : 'partial';
        $status        = $paymentStatus === 'paid' ? 'paid' : 'partial';

        $this->transition($p, [
            'amount_paid'    => $paid,
            'payment_status' => $paymentStatus,
            'status'         => $status,
            'paid_at'        => $paymentStatus === 'paid' ? now() : null,
            'resolved_at'    => $paymentStatus === 'paid' ? now() : null,
            'notes'          => $method ? "Metode: {$method}" : $p->notes,
        ], 'paid', "Pembayaran Rp" . number_format($amount, 0, ',', '.') . " diterima.");

        $this->notifier->penaltyPaid($p);
    }

    // =========================================================
    // RESOLVE RULE
    // =========================================================
    protected function resolveRule(string $violation, int $occurrences): ?AttendancePenaltyRule
    {
        return AttendancePenaltyRule::query()
            ->active()
            ->forViolation($violation)
            ->where('min_occurrences', '<=', $occurrences)
            ->where(fn($q) => $q->whereNull('max_occurrences')->orWhere('max_occurrences', '>=', $occurrences))
            ->orderByDesc('priority')
            ->orderByDesc('min_occurrences')
            ->first();
    }

    // =========================================================
    // INTERNAL
    // =========================================================
    protected function transition(AttendancePenalty $p, array $changes, string $action, string $desc): void
    {
        DB::transaction(function () use ($p, $changes, $action, $desc) {
            $before = $p->only(array_keys($changes));
            $p->fill($changes)->save();
            $after = $p->only(array_keys($changes));

            AttendancePenaltyAuditLog::create([
                'penalty_id'  => $p->id,
                'actor_id'    => Auth::id(),
                'action'      => $action,
                'description' => $desc,
                'before'      => $before,
                'after'       => $after,
            ]);
        });
    }

    protected function audit(AttendancePenalty $p, string $action, string $desc, array $meta = []): void
    {
        AttendancePenaltyAuditLog::create([
            'penalty_id'  => $p->id,
            'actor_id'    => Auth::id(),
            'action'      => $action,
            'description' => $desc,
            'meta'        => $meta,
        ]);
    }

    protected function mapViolation(AttendanceRecord $r): ?string
    {
        return match ($r->status ?? null) {
            'absent'      => ($r->permit_status ?? null) === 'approved' ? null : 'unexcused_absence',
            'late'        => 'late_arrival',
            'early_leave' => 'early_leave',
            'fraud'       => 'attendance_fraud',
            default       => null,
        };
    }

    protected function countOccurrences(AttendanceRecord $r, string $violation): int
    {
        $scopeStart = match ($violation) {
            'unexcused_absence' => now()->startOfMonth(),
            default             => now()->startOfYear(),
        };

        return AttendanceRecord::query()
            ->where('user_id', $r->user_id)
            ->whereIn('status', $this->statusesForViolation($violation))
            ->where('created_at', '>=', $scopeStart)
            ->count();
    }

    protected function statusesForViolation(string $violation): array
    {
        return match ($violation) {
            'unexcused_absence' => ['absent'],
            'late_arrival'      => ['late'],
            'early_leave'       => ['early_leave'],
            'attendance_fraud'  => ['fraud'],
            default             => [],
        };
    }

    protected function buildReason(AttendanceRecord $r, string $violation, int $occurrences): string
    {
        $label    = ucwords(str_replace('_', ' ', $violation));
        $suffix   = $occurrences > 1 ? " (ke-{$occurrences} periode ini)" : '';
        $session  = $r->session->title ?? 'Sesi tidak diketahui';
        return "{$label} pada sesi \"{$session}\"{$suffix}.";
    }

    protected function runEscalation(AttendancePenalty $p, AttendancePenaltyRule $rule): void
    {
        foreach (($rule->escalation_actions ?? []) as $action) {
            $this->audit($p, 'escalated', "Escalation trigger: {$action}", ['action' => $action]);
            $this->notifier->escalation($p, $action);
        }
    }
}