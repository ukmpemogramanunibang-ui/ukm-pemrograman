<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePointRule extends Model
{
    protected $table = 'attendance_point_rules';

    protected $fillable = [
        'code', 'name', 'description', 'points', 'conditions',
        'priority', 'active', 'effective_from', 'effective_until', 'version',
    ];

    protected $casts = [
        'conditions'      => 'array',
        'active'          => 'boolean',
        'effective_from'  => 'date',
        'effective_until' => 'date',
        'version'         => 'integer',
        'points'          => 'integer',
    ];

    public function scopeActiveNow($q)
    {
        return $q->where('active', true)
                 ->where(fn ($w) => $w->whereNull('effective_from')->orWhere('effective_from', '<=', now()))
                 ->where(fn ($w) => $w->whereNull('effective_until')->orWhere('effective_until', '>=', now()));
    }
}