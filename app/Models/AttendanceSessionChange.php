<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSessionChange extends Model
{
    protected $fillable = [
        'session_id', 'change_type', 'before_data', 'after_data',
        'reason', 'created_by',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}