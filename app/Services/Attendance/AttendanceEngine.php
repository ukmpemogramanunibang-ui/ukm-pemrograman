<?php

namespace App\Services\Attendance;

use App\Models\AttendanceSession;
use App\Models\AttendanceTarget;
use App\Models\AttendanceRecord;
use App\Models\AttendanceAuditLog;
use App\Models\AttendanceSecurityEvent;
use App\Models\AttendancePointLedger;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Attendance Engine
 * =================
 * Single source of truth untuk:
 * - Validasi absensi (server-authoritative)
 * - Klasifikasi kehadiran (deterministic)
 * - Perhitungan poin
 * - Anti duplicate & race condition
 *
 * Semua keputusan berada di SERVER. JavaScript hanya presentasi.
 */
class AttendanceEngine
{
    /**
     * Entry point utama: proses absensi user.
     *
     * @param  User                 $user
     * @param  AttendanceSession    $session
     * @param  string               $method   manual|nfc|qr
     * @param  array                $context  ['ip','device','user_agent','officer_id','manual_reason','failed_method']
     * @return array                ['success'=>bool,'code'=>string,'message'=>string,'record'=>?AttendanceRecord]
     */
    public function process(User $user, AttendanceSession $session, string $method, array $context = []): array
    {
        // ============================================================
        // 1. PRIORITY-ORDER VALIDATION
        // ============================================================

        // (1) Sesi valid?
        if (!$session->exists) {
            return $this->reject('invalid_session', 'Sesi absensi tidak ditemukan.');
        }

        // (2) User target?
        $target = AttendanceTarget::where('session_id', $session->id)
            ->where('user_id', $user->id)
            ->where('is_excluded', false)
            ->first();
        if (!$target) {
            $this->logSecurity($user->id, $session->id, 'unauthorized_user', $context);
            return $this->reject('not_target', 'Anda tidak termasuk peserta sesi absensi ini.');
        }

        // (3) Akun aktif?
        if (!$user->is_active) {
            return $this->reject('inactive_user', 'Akun Anda tidak aktif. Hubungi pengurus.');
        }

        // (4) Duplicate check (dilakukan SETELAH sesi dipastikan ada)
        $existing = AttendanceRecord::where('session_id', $session->id)
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->first();
        if ($existing && in_array($existing->status, ['present', 'late', 'late_excused', 'manual', 'historical'])) {
            return [
                'success' => false,
                'code' => 'duplicate',
                'message' => "Anda sudah melakukan absensi pada {$existing->recorded_at->format('H:i:s')}.",
                'record' => $existing,
            ];
        }

        // (5) Metode valid?
        if (!$session->allowsMethod($method)) {
            return $this->reject('method_not_allowed', 'Metode ' . strtoupper($method) . ' tidak tersedia untuk sesi ini.');
        }

        // (6) Timestamp valid? (server time)
        $now = now();
        if (!$session->attendance_open_at || !$session->attendance_close_at) {
            return $this->reject('invalid_time_window', 'Sesi absensi belum memiliki jadwal waktu yang valid.');
        }

        if ($now->lt($session->attendance_open_at)) {
            $this->logSecurity($user->id, $session->id, 'early_attempt', $context);
            return $this->reject('not_open_yet',
                'Absensi belum dibuka. Absensi dapat dilakukan mulai ' . $session->attendance_open_at->format('H:i') . '.');
        }

        if ($now->gte($session->attendance_close_at)) {
            $this->logSecurity($user->id, $session->id, 'late_attempt', $context);
            return $this->reject('expired',
                'Waktu absensi telah berakhir pada ' . $session->attendance_close_at->format('H:i') . '.');
        }

        // (7) Session status valid?
        if (in_array($session->session_status, ['cancelled', 'archived'])) {
            return $this->reject('cancelled', 'Sesi absensi dibatalkan.');
        }

        // ============================================================
        // 2. CLASSIFICATION & SCORING
        // ============================================================
        $permission = app(PermissionResolver::class)->findApproved($user->id, $session->id);
        $classification = $this->classify($session, $now, $permission);
        $points = $this->calculatePoints($classification['status']);

        // ============================================================
        // 3. TRANSACTION: Create record + ledger + audit
        // ============================================================
        try {
            return DB::transaction(function () use (
                $user, $session, $target, $method, $now,
                $classification, $points, $permission, $context
            ) {
                $record = AttendanceRecord::create([
                    'session_id'    => $session->id,
                    'user_id'       => $user->id,
                    'target_id'     => $target->id,
                    'schedule_id'   => $session->schedule_id,
                    'room_id'       => $session->room_id,
                    'snapshot_name' => $target->snapshot_name ?? $user->name,
                    'snapshot_nim'  => $target->snapshot_nim ?? $user->nim,
                    'snapshot_role' => $target->snapshot_role ?? ($user->level->name ?? null),
                    'snapshot_group_name'    => $target->snapshot_group_name,
                    'snapshot_division_name' => $target->snapshot_division_name,
                    'snapshot_room_name'     => $session->room?->name,
                    'attendance_mode' => $target->target_mode ?? $session->attendance_mode,
                    'method'         => $method,
                    'status'         => $classification['status'],
                    'sub_status'     => $classification['sub_status'] ?? null,
                    'recorded_at'    => $now,
                    'points'         => $points,
                    'permission_id'  => $permission?->id,
                    'is_manual_by_officer' => !empty($context['officer_id']),
                    'officer_id'     => $context['officer_id'] ?? null,
                    'manual_reason'  => $context['manual_reason'] ?? null,
                    'failed_method'  => $context['failed_method'] ?? null,
                    'ip_address'     => $context['ip'] ?? request()->ip(),
                    'device_identifier' => $context['device'] ?? null,
                    'user_agent'     => $context['user_agent'] ?? request()->userAgent(),
                    'is_verified'    => true,
                    'verified_at'    => $now,
                    'created_by'     => $user->id,
                ]);

                // Point ledger (immutable)
                if ($points !== 0) {
                    $balance = (int) AttendancePointLedger::where('user_id', $user->id)->sum('points');
                    AttendancePointLedger::create([
                        'user_id'    => $user->id,
                        'session_id' => $session->id,
                        'record_id'  => $record->id,
                        'points'     => $points,
                        'entry_type' => 'earned',
                        'reason'     => 'Absensi ' . $classification['label'],
                        'balance_after' => $balance + $points,
                        'created_by' => $user->id,
                    ]);
                }

                // Audit log
                AttendanceAuditLog::create([
                    'session_id' => $session->id,
                    'actor_id'   => $user->id,
                    'action'     => 'attendance_recorded',
                    'description' => "Absensi via {$method}: {$classification['label']} ({$points} poin)",
                    'after_data' => [
                        'record_id' => $record->id,
                        'status'    => $classification['status'],
                        'points'    => $points,
                        'method'    => $method,
                    ],
                    'ip_address' => $context['ip'] ?? request()->ip(),
                ]);

                // Broadcast event (opsional, kalau pakai broadcasting)
                // event(new AttendanceRecorded($record));

                return [
                    'success'   => true,
                    'code'      => 'success',
                    'message'   => $this->buildSuccessMessage($user, $target, $classification, $points),
                    'record'    => $record,
                    'status'    => $classification['status'],
                    'points'    => $points,
                ];
            });
        } catch (\Throwable $e) {
            Log::error('AttendanceEngine error: ' . $e->getMessage(), [
                'user_id' => $user->id, 'session_id' => $session->id, 'trace' => $e->getTraceAsString()
            ]);
            // Duplicate race condition protection
            if (str_contains($e->getMessage(), 'unique_session_user_record')) {
                return $this->reject('duplicate', 'Anda sudah melakukan absensi.');
            }
            return $this->reject('system_error', 'Terjadi kesalahan sistem. Coba lagi.');
        }
    }

    /**
     * Klasifikasi kehadiran (deterministic)
     */
    protected function classify(AttendanceSession $session, Carbon $now, $permission): array
    {
        $scheduledStart = $session->scheduled_start_at ?? $session->attendance_open_at;
        $gracePeriod = $session->grace_period_minutes ?? 0;
        $lateThreshold = $scheduledStart->copy()->addMinutes($gracePeriod);

        // Cek apakah user punya izin terlambat yang disetujui
        $hasLateExcuse = $permission && in_array($permission->permission_type, ['late_excused', 'late_request'])
            && $permission->status === 'approved';

        if ($now->lte($lateThreshold)) {
            return ['status' => 'present', 'label' => 'Hadir Tepat Waktu', 'sub_status' => null];
        }

        return [
            'status' => $hasLateExcuse ? 'late_excused' : 'late',
            'label'  => $hasLateExcuse ? 'Hadir Terlambat Berizin' : 'Hadir Terlambat',
            'sub_status' => $hasLateExcuse ? 'with_permission' : 'without_permission',
        ];
    }

    /**
     * Scoring engine (deterministic)
     */
    public function calculatePoints(string $status): int
    {
        return match ($status) {
            'present'        => 3,
            'late'           => 1,
            'late_excused'   => 2,
            'excused_absent' => 0,
            'absent_excused' => -1,
            'absent'         => -2,
            default          => 0,
        };
    }

    protected function buildSuccessMessage(User $user, AttendanceTarget $target, array $classification, int $points): string
    {
        $role = $target->snapshot_role ?? '';
        $sign = $points > 0 ? '+' : '';
        return "{$user->name}, {$role}, absensi berhasil. {$classification['label']} ({$sign}{$points} poin).";
    }

    protected function reject(string $code, string $message): array
    {
        return ['success' => false, 'code' => $code, 'message' => $message, 'record' => null];
    }

    protected function logSecurity(?int $userId, ?int $sessionId, string $eventType, array $context, string $severity = 'medium'): void
    {
        try {
            AttendanceSecurityEvent::create([
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'event_type' => $eventType,
                'payload'    => $context,
                'ip_address' => $context['ip'] ?? request()->ip(),
                'user_agent' => $context['user_agent'] ?? request()->userAgent(),
                'severity'   => $severity,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log security event: ' . $e->getMessage());
        }
    }
}