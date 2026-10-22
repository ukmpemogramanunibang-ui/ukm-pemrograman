<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Invoice extends Model

{
     use HasFactory;
protected $fillable = [
    'invoice_number', 'user_id', 'generation_id', 'fee_plan_id',
    'financial_campaign_id', 'created_by', 'title', 'amount',
    'type', 'period', 'standard_due_date', 'adjusted_due_date',
    'final_due_date', 'organization_id', 'status', 'due_date',
    'due_date_reason', 'subtotal', 'penalty', 'discount',
    'total', 'paid_at', 'meta',
];

    protected $casts = [
        'standard_due_date' => 'date',
        'adjusted_due_date' => 'date',
        'final_due_date' => 'date',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];
    protected static function newFactory()
    {
        return \Database\Factories\InvoiceFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $model->organization_id = \App\Services\OrganizationContext::id();
            }
            if (empty($model->invoice_number)) {
                $model->invoice_number = 'INV-' . strtoupper(\Illuminate\Support\Str::random(10));
            }
            if (empty($model->uuid) && \Illuminate\Support\Facades\Schema::hasColumn('invoices', 'uuid')) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::addGlobalScope('organization', function ($builder) {
            $orgId = \App\Services\OrganizationContext::id();
            if ($orgId) {
                $builder->where('invoices.organization_id', $orgId);
            }
        });
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
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
}