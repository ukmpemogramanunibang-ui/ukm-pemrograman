<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'user_id', 'generation_id', 'fee_plan_id', 'financial_campaign_id',
        'type', 'period', 'standard_due_date', 'adjusted_due_date', 'final_due_date',
        'due_date_reason', 'subtotal', 'penalty', 'discount', 'total', 'status', 'paid_at', 'meta'
    ];

    protected $casts = [
        'standard_due_date' => 'date',
        'adjusted_due_date' => 'date',
        'final_due_date' => 'date',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    public function feePlan()
    {
        return $this->belongsTo(FeePlan::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' || ($this->status === 'pending' && $this->final_due_date->isPast());
    }
}