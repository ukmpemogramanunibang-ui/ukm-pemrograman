<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemHealthCheck extends Model
{
    protected $fillable = [
        'status', 'total_checks', 'passed_checks',
        'warning_count', 'error_count', 'checked_at', 'duration_ms', 'details'
    ];

    protected $casts = [
        'details' => 'array',
        'checked_at' => 'datetime',
    ];
}