<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePointLedger extends Model
{
    protected $table = 'attendance_point_ledgers';

    protected $fillable = [
        'user_id', 'session_id', 'record_id', 'points',
        'entry_type', 'reason', 'balance_after',
        'created_by', 'approved_by',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function record() { return $this->belongsTo(AttendanceRecord::class, 'record_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}