<?php

namespace App\View\Composers; // sesuaikan namespace-nya

use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        // ═══════════════════════════════════════════════════
        // PENDING PAYMENTS — defensive
        // ═══════════════════════════════════════════════════
        $pendingPayments = 0;

        // Cek dua namespace berbeda
        $paymentClasses = [
            \App\Models\Financial\PaymentAllocation::class,
            \App\Models\PaymentAllocation::class,
        ];

        foreach ($paymentClasses as $class) {
            if (! class_exists($class)) continue;

            try {
                $pendingPayments = $class::query()
                    ->where('status', 'pending')   // sesuaikan dengan kolom tabel
                    ->count();
                break;
            } catch (\Throwable $e) {
                \Log::debug('[SidebarComposer] pending payments gagal', [
                    'class' => $class,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ═══════════════════════════════════════════════════
        // CRITICAL ATTENDANCE ALERTS — defensive
        // ═══════════════════════════════════════════════════
        $criticalAttendanceCount = 0;

        if (class_exists(\App\Models\AttendanceAuditAlert::class)) {
            try {
                $criticalAttendanceCount = \App\Models\AttendanceAuditAlert::query()
                    ->where('severity', 'critical')
                    ->whereNull('resolved_at')
                    ->count();
            } catch (\Throwable $e) {
                \Log::debug('[SidebarComposer] attendance alerts gagal', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // ═══════════════════════════════════════════════════
        // Share ke view
        // ═══════════════════════════════════════════════════
        $view->with([
            'pending_payments'          => $pendingPayments,
            'critical_attendance_count' => $criticalAttendanceCount,
        ]);
    }
}