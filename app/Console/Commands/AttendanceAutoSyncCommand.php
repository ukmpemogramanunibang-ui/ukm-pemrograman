<?php

namespace App\Console\Commands;

use App\Services\Attendance\AttendanceClockService;
use App\Models\AttendanceSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AttendanceAutoSyncCommand extends Command
{
    protected $signature   = 'attendance:auto-sync';
    protected $description = 'Auto-open/close attendance sessions (failsafe)';

    public function handle(AttendanceClockService $clock): int
    {
        $now = $clock->now();

        $opened = AttendanceSession::where('session_status', 'scheduled')
            ->where('attendance_open_at', '<=', $now)
            ->where('attendance_close_at', '>', $now)
            ->update([
                'session_status'  => 'open',
                'previous_status' => 'scheduled',
                'opened_at'       => $now,
                'state_version'   => DB::raw('state_version + 1'),
            ]);

        $expired = AttendanceSession::whereIn('session_status', ['open', 'extended'])
            ->where('attendance_close_at', '<=', $now)
            ->update([
                'session_status'  => 'expired',
                'previous_status' => DB::raw('session_status'),
                'state_version'   => DB::raw('state_version + 1'),
            ]);

        $this->info("Opened: {$opened} | Expired: {$expired}");

        return self::SUCCESS;
    }
}