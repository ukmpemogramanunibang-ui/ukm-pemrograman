<?php

namespace App\Services\Financial;

use App\Models\User;
use App\Models\Financial\Invoice;
use App\Models\Financial\InvoiceItem;
use App\Models\Financial\FeePlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceGenerator
{
    protected $calendarEngine;

    public function __construct(CalendarEngine $calendarEngine)
    {
        $this->calendarEngine = $calendarEngine;
    }

    /**
     * Generate tagihan bulanan untuk semua anggota aktif
     * 
     * @param string $period Format Y-m
     * @return int Jumlah tagihan yang dibuat
     */
    public function generateMonthlyInvoices($period)
    {
        $startDate = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Ambil semua fee plan aktif
        $feePlans = FeePlan::with('generation')
            ->where('status', 'active')
            ->where('effective_from', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', $startDate);
            })
            ->get();

        if ($feePlans->isEmpty()) {
            Log::info('Tidak ada skema biaya aktif untuk periode ' . $period);
            return 0;
        }

        $count = 0;

        foreach ($feePlans as $feePlan) {
            // Ambil semua user yang termasuk target skema ini
            $users = User::whereHas('generation', function ($q) use ($feePlan) {
                $q->where('id', $feePlan->generation_id);
            })
            ->where('membership_status', 'active')
            ->get();

            foreach ($users as $user) {
                // Cek apakah user sudah punya tagihan untuk periode ini
                $existing = Invoice::where('user_id', $user->id)
                    ->where('period', $period)
                    ->where('fee_plan_id', $feePlan->id)
                    ->exists();

                if ($existing) {
                    continue; // lewati yang sudah ada
                }

                // Buat tagihan
                $standardDueDate = Carbon::createFromFormat('Y-m', $period)->day($feePlan->due_day);
                $adjustedDueDate = $this->calendarEngine->getNextActiveDate($standardDueDate);
                $finalDueDate = $adjustedDueDate ?? $standardDueDate;

                $amount = $feePlan->amount;

                $invoice = Invoice::create([
                    'invoice_number' => $this->generateInvoiceNumber('CASH', $period),
                    'user_id' => $user->id,
                    'generation_id' => $user->generation_id,
                    'fee_plan_id' => $feePlan->id,
                    'type' => 'monthly_cash',
                    'period' => $period,
                    'standard_due_date' => $standardDueDate,
                    'adjusted_due_date' => $adjustedDueDate,
                    'final_due_date' => $finalDueDate,
                    'due_date_reason' => $adjustedDueDate ? 'Hari nonaktif, digeser ke tanggal aktif berikutnya' : null,
                    'subtotal' => $amount,
                    'penalty' => 0,
                    'discount' => 0,
                    'total' => $amount,
                    'status' => 'pending',
                ]);

                // Buat item tagihan
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => "Uang Kas Bulanan - " . $feePlan->name . " ({$period})",
                    'amount' => $amount,
                    'type' => 'cash',
                ]);

                $count++;
            }
        }

        return $count;
    }

    /**
     * Generate nomor invoice otomatis
     */
    protected function generateInvoiceNumber($prefix, $period)
    {
        $last = Invoice::where('invoice_number', 'like', $prefix . '-' . $period . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $last ? intval(substr($last->invoice_number, -4)) + 1 : 1;
        return sprintf("%s-%s-%04d", $prefix, $period, $sequence);
    }
}