<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'schedule_id',
        'room_id',
        'title',
        'description',
        'attendance_type',
        'attendance_mode',
        'scheduled_start_at',
        'scheduled_end_at',
        'attendance_open_at',
        'attendance_close_at',
        'session_end_at',
        'grace_period_minutes',
        'early_open_minutes',
        'extension_minutes',
        'session_status',
        'allowed_methods',
        'target_type',
        'target_data',
        'target_count',
        'shift_name',
        'shift_order',
        'created_by',
        'updated_by',
        'started_at',
        'opened_at',
        'closed_at',
        'extended_at',
        'cancelled_at',
        'cancel_reason',
        'notes',
    ];

    protected $casts = [
        'allowed_methods'      => 'array',
        'target_data'          => 'array',
        'scheduled_start_at'   => 'datetime',
        'scheduled_end_at'     => 'datetime',
        'attendance_open_at'   => 'datetime',
        'attendance_close_at'  => 'datetime',
        'session_end_at'       => 'datetime',
        'started_at'           => 'datetime',
        'opened_at'            => 'datetime',
        'closed_at'            => 'datetime',
        'extended_at'          => 'datetime',
        'cancelled_at'         => 'datetime',
        'target_count'         => 'integer',
        'grace_period_minutes' => 'integer',
        'early_open_minutes'   => 'integer',
        'extension_minutes'    => 'integer',
        'shift_order'          => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($m) {
            if (empty($m->uuid)) {
                $m->uuid = (string) Str::uuid();
            }
            if (empty($m->allowed_methods)) {
                $m->allowed_methods = ['manual', 'nfc', 'qr'];
            }
            if (empty($m->session_status)) {
                $m->session_status = 'scheduled';
            }
        });
    }

    /* ══════════════════════════════════════════════════════════════
     * RELATIONS
     * ══════════════════════════════════════════════════════════════ */
    public function schedule()
    {
        // 🔥 FIX: gunakan RoomSchedule, bukan Schedule
        return $this->belongsTo(RoomSchedule::class, 'schedule_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function targets()
    {
        return $this->hasMany(AttendanceTarget::class, 'session_id');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function permissions()
    {
        return $this->hasMany(AttendancePermission::class, 'session_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AttendanceAuditLog::class, 'session_id');
    }

    public function changes()
    {
        return $this->hasMany(AttendanceSessionChange::class, 'session_id');
    }

    /* ══════════════════════════════════════════════════════════════
     * SCOPES
     * ══════════════════════════════════════════════════════════════ */
    public function scopeActive($q)    { return $q->where('session_status', 'open'); }
    public function scopeScheduled($q) { return $q->where('session_status', 'scheduled'); }
    public function scopeToday($q)     { return $q->whereDate('scheduled_start_at', today()); }

    /* ══════════════════════════════════════════════════════════════
     * HELPERS
     * ══════════════════════════════════════════════════════════════ */
    public function isOpen(): bool
    {
        return $this->session_status === 'open';
    }

    public function isClosed(): bool
    {
        return in_array($this->session_status, ['closed', 'expired', 'cancelled', 'archived']);
    }

    public function isEditable(): bool
    {
        return in_array($this->session_status, ['draft', 'scheduled']);
    }

    public function allowsMethod(string $method): bool
    {
        return in_array($method, $this->allowed_methods ?? []);
    }

    public function canBeOpenedNow(): bool
    {
        if (!$this->attendance_open_at || !$this->attendance_close_at) return false;
        return now()->gte($this->attendance_open_at) && now()->lt($this->attendance_close_at);
    }

    public function isExpired(): bool
    {
        return $this->attendance_close_at && now()->gte($this->attendance_close_at);
    }

    /* ══════════════════════════════════════════════════════════════
     * TARGET DATA ACCESSORS
     * `target_data` adalah JSON dengan struktur:
     * {
     *   "roles": ["owner","pengurus"],
     *   "groups": [1,2,3],
     *   "divisions": [5],
     *   "generations": [2]
     * }
     * ══════════════════════════════════════════════════════════════ */
    public function getTargetRolesAttribute(): array
    {
        return $this->target_data['roles'] ?? [];
    }

    public function getTargetGroupsAttribute(): array
    {
        return $this->target_data['groups'] ?? [];
    }

    public function getTargetDivisionsAttribute(): array
    {
        return $this->target_data['divisions'] ?? [];
    }

    public function getTargetGenerationsAttribute(): array
    {
        return $this->target_data['generations'] ?? [];
    }

    /* ══════════════════════════════════════════════════════════════
     * STATS
     * ══════════════════════════════════════════════════════════════ */
    public function computeStats(): array
    {
        $records = $this->relationLoaded('records')
            ? $this->records
            : $this->records()->get();

        $target = (int) ($this->target_count ?? 0);

        $hadir     = $records->whereIn('status', ['present', 'late', 'late_excused', 'manual', 'historical'])->count();
        $tepat     = $records->where('status', 'present')->count();
        $terlambat = $records->whereIn('status', ['late', 'late_excused'])->count();
        $izin      = $records->whereIn('status', ['excused_absent', 'absent_excused'])->count();
        $bolos     = $records->where('status', 'absent')->count();
        $pending   = $records->where('status', 'pending')->count();
        $belum     = max(0, $target - $records->count());

        $percentage = $target > 0 ? min(100, round(($hadir / $target) * 100)) : 0;

        return [
    'target'       => $target,
    'hadir'        => $hadir,
    'tepat'        => $tepat,           // alias lama
    'tepat_waktu'  => $tepat,           // alias baru (untuk kompatibilitas)
    'terlambat'    => $terlambat,
    'izin'         => $izin,
    'bolos'        => $bolos,
    'pending'      => $pending,
    'belum'        => $belum,
    'percentage'   => $percentage,
    'total_record' => $records->count(),
];
    }

    public function getStatsAttribute(): array
    {
        return $this->computeStats();
    }

    /* ══════════════════════════════════════════════════════════════
     * STATUS ACCESSORS
     * ══════════════════════════════════════════════════════════════ */
    public function getStatusColorAttribute(): string
    {
        return match ($this->session_status) {
            'draft'     => 'gray',
            'scheduled' => 'blue',
            'open'      => 'green',
            'paused'    => 'yellow',
            'extended'  => 'purple',
            'closed'    => 'indigo',
            'expired'   => 'red',
            'cancelled' => 'gray',
            'archived'  => 'gray',
            default     => 'gray',
        };
    }

    public function getTimeStatusAttribute(): string
    {
        if ($this->session_status === 'cancelled') return 'cancelled';
        if ($this->session_status === 'archived')  return 'archived';

        $now = now();

        if (!$this->attendance_open_at || !$this->attendance_close_at) {
            return 'unscheduled';
        }

        if ($now->lt($this->attendance_open_at)) {
            $minutesUntil = $now->diffInMinutes($this->attendance_open_at, false);
            return $minutesUntil <= 15 ? 'opening_soon' : 'upcoming';
        }

        if ($now->lt($this->attendance_close_at)) {
            $minutesUntil = $now->diffInMinutes($this->attendance_close_at, false);
            return $minutesUntil <= 15 ? 'closing_soon' : 'open';
        }

        $eventEnd = $this->session_end_at ?? $this->scheduled_end_at;
        if ($eventEnd && $now->lt($eventEnd)) {
            return 'closed_event_running';
        }

        return 'event_ended';
    }

    public function getTimeStatusLabelAttribute(): string
    {
        return match ($this->time_status) {
            'upcoming'             => 'Akan Datang',
            'opening_soon'         => 'Segera Dibuka',
            'open'                 => 'Absensi Aktif',
            'closing_soon'         => 'Segera Ditutup',
            'closed_event_running' => 'Absen Tutup, Acara Jalan',
            'event_ended'          => 'Acara Selesai',
            'cancelled'            => 'Dibatalkan',
            'archived'             => 'Diarsipkan',
            'unscheduled'          => 'Belum Dijadwalkan',
            default                => 'Tidak Diketahui',
        };
    }

    public function getMinutesUntilOpenAttribute(): ?int
    {
        if (!$this->attendance_open_at) return null;
        $diff = now()->diffInMinutes($this->attendance_open_at, false);
        return $diff > 0 ? $diff : null;
    }

    public function getMinutesUntilCloseAttribute(): ?int
    {
        if (!$this->attendance_close_at) return null;
        $diff = now()->diffInMinutes($this->attendance_close_at, false);
        return $diff > 0 ? $diff : null;
    }

    /* ══════════════════════════════════════════════════════════════
     * ALERTS
     * ══════════════════════════════════════════════════════════════ */
    public function needsAttention(): array
    {
        $reasons = [];
        $stats   = $this->stats;
        $ts      = $this->time_status;

        if ($ts === 'open' && $stats['target'] > 0) {
            $belumRatio = $stats['belum'] / max($stats['target'], 1);
            if ($belumRatio >= 0.5) {
                $reasons[] = [
                    'type'  => 'low_attendance',
                    'level' => 'warning',
                    'text'  => "{$stats['belum']} dari {$stats['target']} peserta belum hadir",
                ];
            }
        }

        if ($ts === 'closing_soon') {
            $reasons[] = [
                'type'  => 'closing_soon',
                'level' => 'urgent',
                'text'  => "Absensi akan ditutup dalam {$this->minutes_until_close} menit",
            ];
        }

        if ($ts === 'opening_soon') {
            $reasons[] = [
                'type'  => 'opening_soon',
                'level' => 'info',
                'text'  => "Absensi akan dibuka dalam {$this->minutes_until_open} menit",
            ];
        }

        if (
            $this->session_status === 'scheduled' &&
            $this->attendance_open_at &&
            now()->gte($this->attendance_open_at) &&
            now()->lt($this->attendance_close_at ?? now()->addYear())
        ) {
            $reasons[] = [
                'type'  => 'should_be_open',
                'level' => 'urgent',
                'text'  => 'Sesi seharusnya sudah dibuka — belum ada aksi',
            ];
        }

        $pendingPerms = $this->relationLoaded('permissions')
            ? $this->permissions->where('status', 'pending')->count()
            : $this->permissions()->where('status', 'pending')->count();

        if ($pendingPerms > 0) {
            $reasons[] = [
                'type'  => 'pending_permissions',
                'level' => 'warning',
                'text'  => "{$pendingPerms} permohonan izin menunggu review",
            ];
        }

        if ($ts === 'unscheduled' && in_array($this->session_status, ['draft', 'scheduled'])) {
            $reasons[] = [
                'type'  => 'no_window',
                'level' => 'info',
                'text'  => 'Window absensi belum ditentukan',
            ];
        }

        return $reasons;
    }
}