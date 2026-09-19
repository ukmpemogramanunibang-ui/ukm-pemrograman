<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSecurityEvent extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'event_type', 'payload',
        'ip_address', 'user_agent', 'severity',
        'resolved', 'resolved_by', 'resolved_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
}