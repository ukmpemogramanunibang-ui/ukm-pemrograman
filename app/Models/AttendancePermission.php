<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttendancePermission extends Model
{
    protected $fillable = [
        'uuid', 'session_id', 'user_id', 'permission_type',
        'reason', 'evidence_path', 'requested_at', 'is_late_request',
        'status', 'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_late_request' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($m) {
            if (empty($m->uuid)) $m->uuid = (string) Str::uuid();
        });
    }

    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function record() { return $this->hasOne(AttendanceRecord::class, 'permission_id'); }
}