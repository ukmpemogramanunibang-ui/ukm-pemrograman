<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceAuditLog extends Model
{
    protected $table = 'attendance_audit_logs';

    protected $fillable = [
        'session_id', 'actor_id', 'action', 'description',
        'before_data', 'after_data', 'reason', 'ip_address',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}