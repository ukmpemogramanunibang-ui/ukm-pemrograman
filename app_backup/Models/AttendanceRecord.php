<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AttendanceRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'session_id', 'user_id', 'target_id', 'schedule_id', 'room_id',
        'snapshot_name', 'snapshot_nim', 'snapshot_role',
        'snapshot_group_name', 'snapshot_division_name', 'snapshot_room_name',
        'attendance_mode', 'method', 'status', 'sub_status',
        'recorded_at', 'points', 'permission_id',
        'is_manual_by_officer', 'officer_id', 'manual_reason', 'failed_method',
        'ip_address', 'device_identifier', 'user_agent',
        'is_verified', 'verified_by', 'verified_at', 'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_manual_by_officer' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($m) {
            if (empty($m->uuid)) $m->uuid = (string) Str::uuid();
        });
    }

    public function session() { return $this->belongsTo(AttendanceSession::class, 'session_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function target() { return $this->belongsTo(AttendanceTarget::class, 'target_id'); }
    public function permission() { return $this->belongsTo(AttendancePermission::class, 'permission_id'); }
    public function officer() { return $this->belongsTo(User::class, 'officer_id'); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }
    public function room() { return $this->belongsTo(Room::class); }
    public function schedule() { return $this->belongsTo(RoomSchedule::class, 'schedule_id'); }
public function penalties()
{
    return $this->hasMany(\App\Models\AttendancePenalty::class, 'attendance_record_id');
}

public function penalty()
{
    return $this->hasOne(\App\Models\AttendancePenalty::class, 'attendance_record_id')->latestOfMany();
}

// ============ SCOPES ============
public function scopeViolation($q)
{
    return $q->whereIn('status', ['absent', 'late', 'early_leave', 'fraud'])
             ->where(fn($qq) => $qq->whereNull('permit_status')->orWhere('permit_status', '!=', 'approved'));
}

// ============ HELPERS ============
public function isViolation(): bool
{
    return in_array($this->status, ['absent', 'late', 'early_leave', 'fraud'], true)
        && ($this->permit_status ?? null) !== 'approved';
}

public function violationType(): ?string
{
    if (!$this->isViolation()) return null;
    return match ($this->status) {
        'absent'      => 'unexcused_absence',
        'late'        => 'late_arrival',
        'early_leave' => 'early_leave',
        'fraud'       => 'attendance_fraud',
        default       => null,
    };
}
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present'        => 'Hadir Tepat Waktu',
            'late'           => 'Hadir Terlambat',
            'late_excused'   => 'Hadir Terlambat Berizin',
            'excused_absent' => 'Izin Tidak Hadir',
            'absent'         => 'Bolos',
            'absent_excused' => 'Izin Tidak Hadir',
            'manual'         => 'Absensi Manual',
            'historical'     => 'Absensi Masa Lalu',
            'rejected'       => 'Ditolak',
            'pending'        => 'Pending Verifikasi',
            'corrected'      => 'Dikoreksi',
            default          => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present'        => 'green',
            'late'           => 'yellow',
            'late_excused'   => 'blue',
            'excused_absent' => 'cyan',
            'absent'         => 'red',
            'absent_excused' => 'orange',
            'manual'         => 'purple',
            'historical'     => 'indigo',
            'pending'        => 'gray',
            'rejected'       => 'red',
            default          => 'gray',
        };
    }
}