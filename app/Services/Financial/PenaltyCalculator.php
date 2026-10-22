<?php

namespace App\Services\Financial;

use App\Models\Financial\Invoice;
use App\Models\Financial\PenaltyRule;
use Carbon\Carbon;

class PenaltyCalculator
{
    /**
     * Calculate penalty for an invoice
     */
    public function calculate(Invoice $invoice)
    {
        if ($invoice->isPaid()) {
            return 0;
        }

        $today = Carbon::today();
        $dueDate = $invoice->final_due_date;
        $daysLate = $dueDate->diffInDays($today, false);

        if ($daysLate <= 0) {
            return 0;
        }

        $penalty = 0;
        $rules = PenaltyRule::active()->orderBy('level')->get();

        foreach ($rules as $rule) {
            if ($daysLate >= $rule->start_day && ($rule->end_day === null || $daysLate <= $rule->end_day)) {
                if ($rule->fixed_amount > 0) {
                    $penalty += $rule->fixed_amount;
                }
                if ($rule->daily_amount > 0) {
                    $daysInRange = $rule->end_day ? min($daysLate, $rule->end_day) - $rule->start_day + 1 : $daysLate - $rule->start_day + 1;
                    $dailyTotal = $daysInRange * $rule->daily_amount;
                    if ($rule->max_amount) {
                        $dailyTotal = min($dailyTotal, $rule->max_amount);
                    }
                    $penalty += $dailyTotal;
                }
            }
        }

        return $penalty;
    }

    /**
     * Apply penalty to invoice (update penalty field)
     */
    public function applyToInvoice(Invoice $invoice)
    {
        $penalty = $this->calculate($invoice);
        if ($penalty != $invoice->penalty) {
            $invoice->penalty = $penalty;
            $invoice->total = $invoice->subtotal + $penalty - $invoice->discount;
            $invoice->save();
        }
        return $penalty;
    }
}