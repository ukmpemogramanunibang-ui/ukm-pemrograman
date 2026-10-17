<?php

namespace App\Services\Attendance;

use App\Models\AttendancePenalty;
use Illuminate\Support\Facades\Log;

class NotificationDispatcher
{
    public function penaltyCreated(AttendancePenalty $p): void
    {
        Log::info('[NOTIF] Denda dibuat', [
            'case'   => $p->case_number,
            'user'   => $p->user_id,
            'amount' => $p->amount,
            'points' => $p->points,
        ]);

        // TODO: kirim ke WhatsApp / Email / In-app
        // Notification::send($p->user, new PenaltyIssuedNotification($p));
    }

    public function penaltyCancelled(AttendancePenalty $p): void
    {
        Log::info('[NOTIF] Denda dibatalkan', ['case' => $p->case_number]);
    }

    public function penaltyWaived(AttendancePenalty $p): void
    {
        Log::info('[NOTIF] Denda dibebaskan', ['case' => $p->case_number]);
    }

    public function penaltyPaid(AttendancePenalty $p): void
    {
        Log::info('[NOTIF] Denda dibayar', [
            'case'   => $p->case_number,
            'paid'   => $p->amount_paid,
        ]);
    }

    public function escalation(AttendancePenalty $p, string $action): void
    {
        Log::warning('[NOTIF] Escalation', [
            'case'   => $p->case_number,
            'action' => $action,
        ]);
    }
}