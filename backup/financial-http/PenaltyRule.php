<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class PenaltyRule extends Model
{
    protected $fillable = ['name', 'level', 'start_day', 'end_day', 'fixed_amount', 'daily_amount', 'max_amount', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}