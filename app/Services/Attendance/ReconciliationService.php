<?php

namespace App\Services\Attendance;

use App\Models\AttendanceSession;
use App\Models\AttendanceTarget;
use App\Models\AttendanceRecord;
use App\Models\AttendancePermission;
use App\Models\AttendancePointLedger;
use App\Models\AttendanceAuditLog;
use Illuminate\Support\Facades\DB;

/**
 * Reconciliation Service
 * ======================
 * Setelah sesi ditutup:
 * - Semua target yang tidak punya record → tentukan status final
 * - Cek izin yang approved → "excused_absent"
 * - Cek izin pending → tetap "pending" atau dianggap hadir?
 * - Tidak ada izin → "absent"
 */
class ReconciliationService
{
    public function reconcile(AttendanceSession $session): array
    {
        $result = ['resolved' => 0, 'excused' => 0, 'absent' => 0, 'pending' => 0];

        DB::transaction(function () use ($session, &$result) {
            $targets = AttendanceTarget::where('session_id', $session->id)
                ->where('is_excluded', false)
                ->whereDoesntHave('record')
                ->get();

            foreach ($targets as $target) {
                $permission = AttendancePermission::where('user_id', $target->user_id)
                    ->where('session_id', $session->id)
                    ->where('status', 'approved')
                    ->first();

                if ($permission) {
                    $status = 'excused_absent';
                    $points = 0;
                    if ($permission->permission_type === 'late_excused') {
                        $status = 'absent_excused';
                        $points = -1;
                    }
                    $result['excused']++;
                } else {
                    $pending = AttendancePermission::where('user_id', $target->user_id)
                        ->where('session_id', $session->id)
                        ->where('status', 'pending')
                        ->exists();

                    if ($pending) {
                        $status = 'pending';
                        $points = 0;
                        $result['pending']++;
                    } else {
                        $status = 'absent';
                        $points = -2;
                        $result['absent']++;
                    }
                }

                $record = AttendanceRecord::create([
                    'session_id' => $session->id,
                    'user_id'    => $target->user_id,
                    'target_id'  => $target->id,
                    'schedule_id' => $session->schedule_id,
                    'room_id'    => $session->room_id,
                    'snapshot_name' => $target->snapshot_name,
                    'snapshot_nim'  => $target->snapshot_nim,
                    'snapshot_role' => $target->snapshot_role,
                    'snapshot_group_name' => $target->snapshot_group_name,
                    'attendance_mode' => $target->target_mode ?? 'undetermined',
                    'method'     => 'auto_system',
                    'status'     => $status,
                    'recorded_at' => now(),
                    'points'     => $points,
                    'permission_id' => $permission?->id,
                    'is_verified' => true,
                    'verified_at' => now(),
                    'created_by' => auth()->id(),
                ]);

                if ($points !== 0) {
                    $balance = (int) AttendancePointLedger::where('user_id', $target->user_id)->sum('points');
                    AttendancePointLedger::create([
                        'user_id' => $target->user_id,
                        'session_id' => $session->id,
                        'record_id'  => $record->id,
                        'points'     => $points,
                        'entry_type' => 'penalty',
                        'reason'     => 'Rekonsiliasi otomatis sesi ' . $session->title,
                        'balance_after' => $balance + $points,
                    ]);
                }

                $result['resolved']++;
            }

            AttendanceAuditLog::create([
                'session_id' => $session->id,
                'actor_id'   => auth()->id(),
                'action'     => 'reconciliation_run',
                'description' => "Rekonsiliasi: {$result['resolved']} record diselesaikan",
                'after_data' => $result,
            ]);
        });

        return $result;
    }
}