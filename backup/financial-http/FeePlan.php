<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class FeePlan extends Model
{
    protected $fillable = ['generation_id', 'name', 'amount', 'frequency', 'due_day', 'effective_from', 'effective_until', 'status'];

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}