<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Financial\Invoice;
use App\Services\Financial\PenaltyCalculator;

class ApplyPenalties extends Command
{
    protected $signature = 'financial:apply-penalties';
    protected $description = 'Apply late penalties to overdue invoices';

    public function handle(PenaltyCalculator $calculator)
    {
        $invoices = Invoice::where('status', 'pending')
            ->where('final_due_date', '<', now())
            ->get();

        foreach ($invoices as $invoice) {
            $calculator->applyToInvoice($invoice);
        }

        $this->info('Penalties applied to ' . $invoices->count() . ' invoices.');
    }
}