<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Financial\InvoiceGenerator;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'financial:generate-invoices {period?}';
    protected $description = 'Generate monthly invoices for all active members';

    public function handle(InvoiceGenerator $generator)
    {
        $period = $this->argument('period') ?? now()->format('Y-m');
        $generator->generateMonthlyInvoices($period);
        $this->info('Invoices generated for period ' . $period);
    }
}