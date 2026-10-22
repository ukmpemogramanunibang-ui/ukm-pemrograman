<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'external_id',
        'amount',
        'payment_method',
        'gateway',
        'status',
        'paid_at',
        'payload',
        'created_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function attempts()
    {
        return $this->hasMany(PaymentAttempt::class);
    }
    protected static function booted()
{
    static::updated(function ($payment) {
        // Jika status berubah menjadi paid
        if ($payment->wasChanged('status') && $payment->status === 'paid') {
            // Ambil semua alokasi invoice dari payment ini
            $allocations = $payment->allocations;

            foreach ($allocations as $allocation) {
                $invoice = Invoice::find($allocation->invoice_id);
                if (!$invoice) continue;

                // Hitung total pembayaran yang sudah dialokasikan untuk invoice ini
                $totalPaid = PaymentAllocation::where('invoice_id', $invoice->id)
                    ->whereHas('paymentTransaction', function ($q) {
                        $q->where('status', 'paid');
                    })
                    ->sum('amount');

                if ($totalPaid >= $invoice->total) {
                    $invoice->status = 'paid';
                    $invoice->paid_at = now();
                    $invoice->save();
                }
            }
        }
    });
}
}