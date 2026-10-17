<?php

namespace App\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        // ═══════════════════════════════════════════════════
        // PENDING PAYMENTS — kolom 'status' TIDAK ADA di
        // tabel payment_allocations. Pakai kolom yang valid:
        // => hitung dari relasi ke invoices / payment_transactions
        //    atau cukup set 0 kalau tidak dipakai.
        // ═══════════════════════════════════════════════════
        $pendingPayments = 0;

        // Opsi A: Ambil dari PaymentTransaction (kalau ada kolom status di sana)
        // $class = \App\Models\Financial\PaymentTransaction::class;
        // if (class_exists($class)) {
        //     try { $pendingPayments = $class::where('status', 'pending')->count(); }
        //     catch (\Throwable $e) {}
        // }

        // Opsi B (default): hitung dari invoices yang belum lunas (kalau ada kolomnya)
        // $invoiceClass = \App\Models\Financial\Invoice::class;
        // if (class_exists($invoiceClass)) {
        //     try {
        //         $pendingPayments = $invoiceClass::whereIn('status', ['unpaid', 'pending'])->count();
        //     } catch (\Throwable $e) {}
        // }

        // Opsi C (fallback aman): biarkan 0 — tidak ada query sama sekali
        // (karena kolom 'status' memang tidak ada di payment_allocations)

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