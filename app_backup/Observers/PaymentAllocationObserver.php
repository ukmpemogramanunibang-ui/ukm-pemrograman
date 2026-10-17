<?php

namespace App\Observers;

use App\Models\Financial\PaymentAllocation;
use App\Models\Financial\Invoice;

class PaymentAllocationObserver
{
    public function created(PaymentAllocation $allocation)
    {
        // Ambil invoice terkait
        $invoice = Invoice::find($allocation->invoice_id);
        if (!$invoice) return;

        // Hitung total pembayaran yang sudah dialokasikan untuk invoice ini
        $totalPaid = PaymentAllocation::where('invoice_id', $invoice->id)
            ->whereHas('paymentTransaction', function ($q) {
                $q->where('status', 'paid');
            })
            ->sum('amount');

        // Jika total pembayaran >= total tagihan, update status invoice
        if ($totalPaid >= $invoice->total) {
            $invoice->status = 'paid';
            $invoice->paid_at = now();
            $invoice->save();

            // Kirim notifikasi (opsional)
            // ...
        }
    }
}