<?php

namespace App\Services\Financial;

use App\Models\Financial\Invoice;
use App\Models\Financial\PaymentTransaction;
use App\Models\Financial\PaymentAllocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentAllocator
{
    /**
     * Allocate a payment to invoices (prepaid logic)
     */
    public function allocate(User $user, PaymentTransaction $transaction, $amount)
    {
        DB::transaction(function () use ($user, $transaction, $amount) {
            // Get unpaid invoices ordered by due date
            $invoices = Invoice::where('user_id', $user->id)
                ->where('status', 'pending')
                ->orderBy('final_due_date')
                ->get();

            $remaining = $amount;

            foreach ($invoices as $invoice) {
                if ($remaining <= 0) break;

                $due = $invoice->total - $invoice->payments()->sum('amount');
                if ($due <= 0) continue;

                $allocated = min($remaining, $due);
                $remaining -= $allocated;

                PaymentAllocation::create([
                    'payment_transaction_id' => $transaction->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $allocated,
                ]);

                // Update invoice status if fully paid
                if ($invoice->payments()->sum('amount') >= $invoice->total) {
                    $invoice->status = 'paid';
                    $invoice->paid_at = now();
                    $invoice->save();
                }
            }

            // If there is remaining, create prepaid balance (optional)
            if ($remaining > 0) {
                // Create a prepaid record or store in a 'prepaid_balance' field
                // For simplicity, we'll create a dummy invoice or store in meta
                // You can implement a "prepaid balance" table
            }
        });
    }
}