<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'room_schedules';

    protected $fillable = [
        'room_id',
        'created_by',
        'title',
        'activity_type',
        'description',
        'recurrence_type',
        'recurrence_days',
        'start_datetime',
        'end_datetime',
        'end_date',
        'requires_attendance',
        'status',
        'images',

        // ── kolom baru (lihat migration di langkah 3)
        'semester',
        'semester_start',
        'semester_end',
        'academic_year',
        'schedule_type',

        // Audience / attendance
        'audience_type',
        'audience_ids',
        'audience_count',
        'attendance_mode',
        'auto_create_attendance',
        'requires_review',
        'attendance_session_id',

        // Info luar jam operasional
        'outside_operational_reason',
        'notes',
    ];

    protected $casts = [
        'recurrence_days'         => 'array',
        'images'                  => 'array',
        'audience_ids'            => 'array',
        'start_datetime'          => 'datetime',
        'end_datetime'            => 'datetime',
        'end_date'                => 'date',
        'auto_create_attendance'  => 'boolean',
        'requires_review'         => 'boolean',
        'requires_attendance'     => 'boolean',
    ];

    /* ──────────────── RELATIONS ──────────────── */

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities()
    {
        return $this->hasMany(ScheduleActivity::class, 'schedule_id')
            ->orderBy('day')
            ->orderBy('stage');
    }

    /* ──────────────── HELPERS ──────────────── */

    /**
     * Resolve daftar user penerima berdasarkan audience_type + audience_ids.
     */
    public function resolveAudience()
    {
        $base = User::query()
            ->where('is_active', true)
            ->whereNull('deleted_at');

        if ($this->audience_type === 'all' || empty($this->audience_ids)) {
            return $base->where('level_id', '!=', 1)->get();
        }

        return match ($this->audience_type) {
            'kelompok' => method_exists(User::class, 'groups')
                ? $base->whereHas('groups', fn ($q) => $q->whereIn('groups.id', $this->audience_ids))->get()
                : collect(),

            'generasi' => \Schema::hasColumn('users', 'generation_id')
                ? $base->whereIn('generation_id', $this->audience_ids)->get()
                : collect(),

            'pengurus', 'role' => $base->whereIn('level_id', $this->audience_ids)->get(),

            'lms_group' => method_exists(User::class, 'lmsGroups')
                ? $base->whereHas('lmsGroups', fn ($q) => $q->whereIn('lms_groups.id', $this->audience_ids))->get()
                : collect(),

            default => collect(),
        };
    }
}