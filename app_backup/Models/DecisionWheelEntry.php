<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionWheelEntry extends Model
{
    protected $fillable = ['wheel_id', 'name', 'weight', 'position', 'is_active'];

    protected $casts = [
        'weight'    => 'integer',
        'position'  => 'integer',
        'is_active' => 'boolean',
    ];

    public function wheel(): BelongsTo
    {
        return $this->belongsTo(DecisionWheel::class, 'wheel_id');
    }
}