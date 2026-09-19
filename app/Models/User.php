<?php

namespace App\Models;

use App\Models\Jabatan;
use App\Models\JabatanUser;
use App\Models\Level;
use App\Models\LoginHistory;
use App\Models\Major;
use App\Models\Notification; // ← import for notifications()
use App\Models\PointHistory;
use App\Models\Status;
use App\Models\University;
use App\Models\UserQrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route; 
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;
    use SoftDeletes;

    protected string $guard_name = 'web';

    // ✅ Corrected $fillable – no duplicates, no stray text
    protected $fillable = [
        'user_uuid',
        'nim',
        'name',
        'username',
        'email',
        'phone',
        'university_id',
        'major_id',
        'level_id',
        'status_id',
        'password',
        'pin',
        'qr_token',
        'account_type',
        'member_number',
        'ukm_id',
        'event_id',
        'is_verified',
        'verified_at',
        'email_verified_at',
        'failed_login_attempts',
        'locked_until',
        'last_password_confirmed_at',
        'password_changed_at',
        'timezone',
        'locale',
        'preferences',
        'settings',
        'avatar_url',
        'avatar_updated_at',
        'gender',
        'birth_date',
        'address',
        'bio',
        'cover_photo',
        'profile_completion',
        'profile_completed_at',
        'receive_email_notification',
        'receive_whatsapp_notification',
        'is_active',
        'is_online',
        'last_login_at',
        'last_login_ip',
        'last_seen_at',
        'created_ip',
        'updated_ip',
        'created_device',
        'updated_device',
        'login_devices',
        'last_failed_login_at',
        'last_failed_login_ip',
        'last_password_reset_at',
        'last_profile_update_at',
        'semester',
        'portfolio_url',
        'social_media',
        'skills',

    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pin',
        'qr_token',
    ];

    protected $appends = [
        'rank',
        'total_points',
    ];

    protected function casts(): array
    {
        return [
            'password'                    => 'hashed',
            'pin'                         => 'hashed',
            'email_verified_at'           => 'datetime',
            'verified_at'                 => 'datetime',
            'last_login_at'               => 'datetime',
            'last_seen_at'                => 'datetime',
            'password_changed_at'         => 'datetime',
            'last_password_confirmed_at'  => 'datetime',
            'avatar_updated_at'           => 'datetime',
            'last_password_reset_at'      => 'datetime',
            'last_profile_update_at'      => 'datetime',
            'last_failed_login_at'        => 'datetime',
            'locked_until'                => 'datetime',
            'birth_date'                  => 'date',
            'preferences'                 => 'array',
            'settings'                    => 'array',
            'login_devices'               => 'array',
            'is_verified'                 => 'boolean',
            'is_active'                   => 'boolean',
            'is_online'                   => 'boolean',
            'receive_email_notification'  => 'boolean',
            'receive_whatsapp_notification' => 'boolean',
            'social_media' => 'array',
            'skills' => 'array',
        ];
    }
    public function invoices()
{
    return $this->hasMany(\App\Models\Financial\Invoice::class);
}

public function announcements()
{
    return $this->belongsToMany(\App\Models\Announcement::class, 'announcement_user')
                ->withPivot('created_at')
                ->withTimestamps();
}
public function qrCode()
{
    return $this->hasOne(UserQrCode::class);
}
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->user_uuid)) {
                $user->user_uuid = (string) Str::uuid();
            }
            if (empty($user->member_number) && $user->email !== 'owner@ukm.ac.id') {
                $user->member_number = self::generateMemberNumber();
            }
        });
    }

    public static function generateMemberNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->format('y');
            $last = self::query()
                ->whereNotNull('member_number')
                ->latest('id')
                ->lockForUpdate()
                ->first();
            $number = 1;
            if ($last && $last->member_number) {
                $number = (int) substr($last->member_number, -6) + 1;
            }
            return sprintf('UKM%s%06d', $year, $number);
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Role checks
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function isKetua(): bool
    {
        return $this->hasRole('ketua');
    }

    // Status cepat
    public function isOnline(): bool
    {
        return $this->is_online;
    }

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->verified_at);
    }

    public function hasPin(): bool
    {
        return ! empty($this->pin);
    }

    // Relationships
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function jabatans(): BelongsToMany
{
    return $this->belongsToMany(Jabatan::class, 'jabatan_user')
        ->withPivot([
            'is_primary',
            'status',
            'organizational_period_id',
            'start_date',
            'end_date',
            'approved_at',
            'approved_by',
            'created_by',
            'updated_by',
            'notes',
        ])
        ->withTimestamps();
}
public function getRoleLayout(): string
{
    return match ($this->level_id) {
        1 => 'owner',
        2 => 'superadmin',
        3 => 'ketua',
        4 => 'pengurus',
        5 => 'anggota',
        6 => 'anggota-umum',
        default => 'guest',
    };
}
public function recoverySnapshot()
{
    return $this->hasOne(UserQrRecoverySnapshot::class, 'user_uuid', 'user_uuid');
}
    public function createdJabatans(): HasMany
    {
        return $this->hasMany(JabatanUser::class, 'created_by');
    }

    public function updatedJabatans(): HasMany
    {
        return $this->hasMany(JabatanUser::class, 'updated_by');
    }

    public function revokedJabatans(): HasMany
    {
        return $this->hasMany(JabatanUser::class, 'revoked_by');
    }

    public function approvedJabatans(): HasMany
    {
        return $this->hasMany(JabatanUser::class, 'approved_by');
    }

    public function qrCodes()
    {
    return $this->hasMany(\App\Models\UserQrCode::class, 'user_id');
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function pointHistories(): HasMany
    {
        return $this->hasMany(PointHistory::class);
    }

    public function givenPoints(): HasMany
    {
        return $this->hasMany(PointHistory::class, 'given_by');
    }

    public function approvedPoints(): HasMany
    {
        return $this->hasMany(PointHistory::class, 'approved_by');
    }
// Di dalam model User
public function getLevelNameAttribute()
{
    return $this->level ? $this->level->name : 'guest';
}
    // Accessors
    public function getRankAttribute(): string
    {
        $total = $this->total_points;
        return match (true) {
            $total < 100  => 'Pemula',
            $total < 300  => 'Pejuang',
            $total < 600  => 'Terampil',
            $total < 1000 => 'Jago',
            $total < 1500 => 'Sepuh',
            $total < 2500 => 'Legenda',
            $total < 4000 => 'Mythic',
            default       => 'Immortal',
        };
    }

    public function getTotalPointsAttribute(): int
    {
        return (int) $this->pointHistories()
            ->where('status', 'approved')
            ->sum('points');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('users')
            ->logOnly(['name', 'email', 'phone', 'level_id', 'status_id', 'is_active'])
            ->logExcept(['password', 'pin'])
            ->dontLogIfAttributesChangedOnly(['last_seen_at', 'last_login_at', 'is_online'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

public function getDashboardRoute(): string
{
    $routeName = match ($this->level_id) {
        1 => 'owner.dashboard',
        2 => 'superadmin.dashboard',
        3 => 'ketua.dashboard',
        4 => 'pengurus.dashboard',
        5 => 'anggota.dashboard',
        6 => 'guest.dashboard',
        default => null,
    };

    if ($routeName && Route::has($routeName)) {   // ← sekarang Route = facade Illuminate
        return route($routeName);
    }

    return route('landing');
}
// Relasi ke percakapan sebagai peserta
public function conversations()
{
    return $this->belongsToMany(Conversation::class, 'conversation_participants')
        ->withPivot('role', 'joined_at', 'left_at', 'last_read_at', 'is_muted')
        ->withTimestamps();
}

// Relasi ke pesan yang dikirim
public function messages()
{
    return $this->hasMany(Message::class, 'sender_id');
}

// Relasi ke percakapan yang dibuat
public function createdConversations()
{
    return $this->hasMany(Conversation::class, 'created_by');
}
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
    public function groupAnnouncements()
{
    return $this->hasMany(GroupAnnouncement::class, 'created_by');
}
public function divisionMembers()
{
    return $this->hasMany(DivisionMember::class);
}

public function divisions()
{
    return $this->belongsToMany(Division::class, 'division_members')
                ->withPivot('position', 'status', 'joined_at', 'left_at')
                ->withTimestamps();
}
public function nfcCredential()
{
    return $this->hasOne(NfcCredential::class)->latestOfMany();
}
public function gameSessions()
{
    return $this->hasMany(GameSession::class);
}
// === ATTENDANCE RELATIONS ===
public function attendanceRecords()
{
    return $this->hasMany(\App\Models\AttendanceRecord::class);
}

public function attendanceTargets()
{
    return $this->hasMany(\App\Models\AttendanceTarget::class);
}

public function attendancePermissions()
{
    return $this->hasMany(\App\Models\AttendancePermission::class);
}

public function attendancePointLedgers()
{
    return $this->hasMany(\App\Models\AttendancePointLedger::class);
}

public function attendanceSessionsCreated()
{
    return $this->hasMany(\App\Models\AttendanceSession::class, 'created_by');
}

/**
 * Hitung total poin absensi (dari ledger - immutable)
 */
public function getAttendancePointsAttribute(): int
{
    return (int) $this->attendancePointLedgers()->sum('points');
}

/**
 * Statistik kehadiran user
 */
public function getAttendanceStatsAttribute(): array
{
    $records = $this->attendanceRecords()
        ->whereIn('status', ['present','late','late_excused','excused_absent','absent','absent_excused'])
        ->get();

    $total = max(1, $records->count());
    return [
        'total'   => $records->count(),
        'hadir'   => $records->whereIn('status', ['present','late','late_excused'])->count(),
        'tepat'   => $records->where('status', 'present')->count(),
        'telat'   => $records->whereIn('status', ['late','late_excused'])->count(),
        'izin'    => $records->whereIn('status', ['excused_absent','absent_excused'])->count(),
        'bolos'   => $records->where('status', 'absent')->count(),
        'rate'    => round(($records->whereIn('status', ['present','late','late_excused'])->count() / $total) * 100, 1),
    ];
}
}