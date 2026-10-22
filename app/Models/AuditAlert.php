<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditAlert extends Model
{
    protected $table = 'audit_alerts';

    protected $fillable = [
        'alert_uuid', 'type', 'severity', 'title', 'message',
        'context', 'related_event_uuid', 'acknowledged_at', 'acknowledged_by',
    ];

    protected $casts = [
        'context'         => 'array',
        'acknowledged_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $a) => $a->alert_uuid ??= (string) Str::uuid());
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}