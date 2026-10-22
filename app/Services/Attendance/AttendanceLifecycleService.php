<?php

namespace App\Services\Attendance;

use App\Models\AttendanceSession;
use App\Models\AttendanceAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Validation\ValidationException;

/**
 * State Machine untuk lifecycle AttendanceSession.
 *
 * SEMUA transisi status WAJIB lewat sini.
 * Controller tidak boleh update session_status langsung.
 */
class AttendanceLifecycleService
{
    /** Transisi yang sah: dari → ke[] */
    protected array $allowedTransitions = [
        'draft'      => ['scheduled', 'cancelled'],
        'scheduled'  => ['open', 'cancelled'],
        'open'       => ['paused', 'extended', 'closed', 'cancelled'],
        'paused'     => ['open', 'cancelled'],
        'extended'   => ['closed', 'cancelled', 'paused'],
        'closed'     => ['reconciled', 'archived'],
        'expired'    => ['closed', 'archived'],
        'cancelled'  => ['archived'],
        'reconciled' => ['archived'],
        'archived'   => [],
    ];

    public function __construct(
        protected AttendanceClockService $clock,
        protected AttendanceSecurityService $security,
    ) {}

    /**
     * Jalankan transisi status dengan guard + audit + concurrency control.
     *
     * @param  int|null  $expectedVersion  Optimistic locking (state_version)
     * @throws ValidationException
     */
    public function transition(
        AttendanceSession $session,
        string $to,
        User $actor,
        array $context = [],
        ?int $expectedVersion = null,
    ): AttendanceSession {
        return DB::transaction(function () use ($session, $to, $actor, $context, $expectedVersion) {

            /* ── 1. LOCK ── */
            $locked = AttendanceSession::whereKey($session->id)->lockForUpdate()->firstOrFail();

            /* ── 2. OPTIMISTIC VERSION CHECK ── */
            if ($expectedVersion !== null && (int) $locked->state_version !== (int) $expectedVersion) {
                throw ValidationException::withMessages([
                    'state' => "Sesi telah berubah oleh pengguna lain (v{$locked->state_version}). Silakan refresh.",
                ]);
            }

            /* ── 3. VALIDASI TRANSISI ── */
            $from = $locked->session_status;
            $this->assertTransitionAllowed($from, $to);

            /* ── 4. SECURITY CONTEXT ── */
            $drift = $this->security->evaluateContext($actor, $context);

            /* ── 5. APPLY ── */
            $before = $locked->toArray();

            $locked->fill(array_merge(
                $this->attributeFor($to),
                [
                    'previous_status' => $from,
                    'state_version'   => $locked->state_version + 1,
                    'security_context'=> $drift,
                    'updated_by'      => $actor->id,
                ]
            ));

            $locked->save();

            /* ── 6. AUDIT ── */
            $this->writeAudit($locked, $from, $to, $actor, $before, $context);

            return $locked->refresh();
        });
    }

    /* ============================================================
     * TRANSITION HELPERS
     * ============================================================ */

    protected function assertTransitionAllowed(string $from, string $to): void
    {
        $allowed = $this->allowedTransitions[$from] ?? [];

        if (!in_array($to, $allowed, true)) {
            throw ValidationException::withMessages([
                'state' => "Transisi tidak sah: {$from} → {$to}.",
            ]);
        }
    }

    protected function attributeFor(string $status): array
    {
        $now = $this->clock->now();

        return match ($status) {
            'open'       => ['session_status' => 'open',     'opened_at'   => $now],
            'paused'     => ['session_status' => 'paused',   'paused_at'   => $now],
            'extended'   => ['session_status' => 'extended', 'extended_at' => $now],
            'closed'     => ['session_status' => 'closed',   'closed_at'   => $now],
            'cancelled'  => ['session_status' => 'cancelled','cancelled_at'=> $now],
            'reconciled' => ['session_status' => 'reconciled','reconciled_at' => $now],
            default      => ['session_status' => $status],
        };
    }

    protected function writeAudit(
        AttendanceSession $session,
        string $from,
        string $to,
        User $actor,
        array $before,
        array $context,
    ): void {
        AttendanceAuditLog::create([
            'event_id'    => (string) \Illuminate\Support\Str::uuid(),
            'request_id'  => request()?->header('X-Request-Id'),
            'session_id'  => $session->id,
            'actor_id'    => $actor->id,
            'actor_role'  => $actor->level?->slug ?? $actor->level?->name,
            'action'      => "session.{$to}",
            'entity_type' => AttendanceSession::class,
            'entity_id'   => $session->id,
            'description' => "Transisi {$from} → {$to}",
            'before_data' => $before,
            'after_data'  => $session->toArray(),
            'before_hash' => hash('sha256', json_encode($before)),
            'after_hash'  => hash('sha256', json_encode($session->toArray())),
            'reason'      => $context['reason'] ?? null,
            'ip_address'  => request()?->ip(),
            'created_at'  => $this->clock->now(),
            'updated_at'  => $this->clock->now(),
        ]);
    }

    /* ============================================================
     * CONVENIENCE METHODS
     * ============================================================ */

    public function open(AttendanceSession $s, User $a, array $ctx = [], ?int $v = null): AttendanceSession
    {
        return $this->transition($s, 'open', $a, $ctx, $v);
    }

    public function close(AttendanceSession $s, User $a, array $ctx = [], ?int $v = null): AttendanceSession
    {
        return $this->transition($s, 'closed', $a, $ctx, $v);
    }

    public function cancel(AttendanceSession $s, User $a, array $ctx = [], ?int $v = null): AttendanceSession
    {
        return $this->transition($s, 'cancelled', $a, $ctx, $v);
    }

    public function pause(AttendanceSession $s, User $a, array $ctx = [], ?int $v = null): AttendanceSession
    {
        return $this->transition($s, 'paused', $a, $ctx, $v);
    }

    public function resume(AttendanceSession $s, User $a, array $ctx = [], ?int $v = null): AttendanceSession
    {
        return $this->transition($s, 'open', $a, $ctx, $v);
    }

    public function extend(AttendanceSession $s, User $a, int $minutes, array $ctx = [], ?int $v = null): AttendanceSession
    {
        return DB::transaction(function () use ($s, $a, $minutes, $ctx, $v) {
            $locked = AttendanceSession::whereKey($s->id)->lockForUpdate()->firstOrFail();

            if ($v !== null && (int) $locked->state_version !== $v) {
                throw ValidationException::withMessages(['state' => 'Sesi telah berubah. Refresh.']);
            }

            // Extension policy
            $maxExtensions  = config('attendance.max_extensions', 3);
            $maxTotalExtend = config('attendance.max_total_extension_minutes', 60);

            $countExtensions = $locked->auditLogs()->where('action', 'session.extended')->count();
            if ($countExtensions >= $maxExtensions) {
                throw ValidationException::withMessages([
                    'minutes' => "Batas maksimum {$maxExtensions}x perpanjangan tercapai.",
                ]);
            }

            $totalExtend = ($locked->extension_minutes ?? 0) + $minutes;
            if ($totalExtend > $maxTotalExtend) {
                throw ValidationException::withMessages([
                    'minutes' => "Total perpanjangan tidak boleh melebihi {$maxTotalExtend} menit.",
                ]);
            }

            $locked->fill([
                'attendance_close_at' => $locked->attendance_close_at->copy()->addMinutes($minutes),
                'extension_minutes'   => $totalExtend,
                'session_status'      => 'extended',
                'extended_at'         => $this->clock->now(),
                'previous_status'     => $locked->session_status,
                'state_version'       => $locked->state_version + 1,
                'updated_by'          => $a->id,
            ])->save();

            AttendanceAuditLog::create([
                'event_id'    => (string) \Illuminate\Support\Str::uuid(),
                'session_id'  => $locked->id,
                'actor_id'    => $a->id,
                'actor_role'  => $a->level?->slug ?? $a->level?->name,
                'action'      => 'session.extended',
                'description' => "Diperpanjang {$minutes} menit",
                'reason'      => $ctx['reason'] ?? null,
                'after_data'  => ['extension_minutes' => $totalExtend],
                'ip_address'  => request()?->ip(),
                'created_at'  => $this->clock->now(),
                'updated_at'  => $this->clock->now(),
            ]);

            return $locked->refresh();
        });
    }
}