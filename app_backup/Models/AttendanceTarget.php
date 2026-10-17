<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceTarget extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'group_id', 'division_id',
        'target_mode', 'is_excluded', 'exclude_reason',
        'snapshot_name', 'snapshot_nim', 'snapshot_role',
        'snapshot_group_name', 'snapshot_division_name',
    ];

    protected $casts = ['is_excluded' => 'boolean'];

    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function group() { return $this->belongsTo(Group::class); }
    public function division() { return $this->belongsTo(Division::class); }
    public function record() { return $this->hasOne(AttendanceRecord::class, 'target_id'); }
}