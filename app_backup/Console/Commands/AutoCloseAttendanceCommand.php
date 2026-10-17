<?php

namespace App\Console\Commands;

use App\Models\AttendanceSession;
use App\Services\Audit\AuditService;
use Illuminate\Console\Command;

class AutoCloseAttendanceCommand extends Command
{
    protected $signature = 'attendance:auto-close';
    protected $description = 'Auto-close attendance sessions yang sudah melewati deadline';

    public function handle()
    {
        $sessions = AttendanceSession::query()
            ->where('session_status', 'open')
            ->where('attendance_deadline_at', '<=', now())
            ->get();

        foreach ($sessions as $session) {
            $before = ['session_status' => $session->session_status];

            $session->update(['session_status' => 'closed']);

            // ⭐ Log sebagai SISTEM (bukan user)
            AuditService::record([
                'actor_type' => 'scheduler',
                'actor_name' => 'SYSTEM',
                'action'     => 'SESSION_AUTO_CLOSED',
                'severity'   => 'info',
                'reason'     => 'Attendance deadline reached',
                'session_id' => $session->id,
                'before'     => $before,
                'after'      => ['session_status' => 'closed'],
            ]);
        }

        $this->info("Auto-closed {$sessions->count()} sessions.");
    }
}