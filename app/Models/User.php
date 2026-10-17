<?php

namespace App\Models;

use App\Models\AttendancePointLedger;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\AttendanceTarget;
use App\Models\Conversation;
use App\Models\Division;
use App\Models\DivisionMember;
use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\GroupMember;
use App\Models\Jabatan;
use App\Models\JabatanUser;
use App\Models\Level;
use App\Models\LoginHistory;
use App\Models\Major;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\PointHistory;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\Status;
use App\Models\University;
use App\Models\UserQrCode;
use App\Models\UserQrRecoverySnapshot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model — Multi-Organization Ready
 * ═════════════════════════════════════════
 *
 * Prinsip desain:
 *  1. level_id  = kewenangan PLATFORM (owner, superadmin, dst)
 *  2. OrganizationMembership = kewenangan PER ORGANISASI
 *  3. Keduanya TIDAK dicampur — ketua BEM ≠ ketua HIMA ≠ ketua UKM
 *
 * Perubahan besar dari versi lama:
 *  - Guard "hanya 1 ketua global" DIHAPUS (dipindah ke OrganizationLeadershipService)
 *  - Duplikasi method getDashboardRoute() diperbaiki
 *  - $appends dihapus (N+1 query fix)
 *  - Helper baru: isActive(), isLeaderOf(), activeOrganization()
 *  - Semua return type dideklarasikan
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;
    use SoftDeletes;
    use HasApiTokens;

    protected string $guard_name = 'web';

    /* ═══════════════════════════════════════════════════════
     | MASS ASSIGNMENT
     * ═══════════════════════════════════════════════════════ */

    protected $fillable = [
        'user_uuid', 'nim', 'name', 'username', 'email', 'phone',
        'university_id', 'major_id', 'level_id', 'status_id',
        'password', 'pin', 'qr_token', 'account_type',
        'member_number', 'ukm_id', 'event_id',
        'is_verified', 'verified_at', 'email_verified_at',
        'failed_login_attempts', 'locked_until',
        'last_password_confirmed_at', 'password_changed_at',
        'timezone', 'locale', 'preferences', 'settings',
        'avatar_url', 'avatar_updated_at',
        'gender', 'birth_date', 'address', 'bio', 'cover_photo',
        'profile_completion', 'profile_completed_at',
        'receive_email_notification', 'receive_whatsapp_notification',
        'is_active', 'is_online',
        'last_login_at', 'last_login_ip', 'last_seen_at',
        'created_ip', 'updated_ip', 'created_device', 'updated_device', 'login_devices',
        'last_failed_login_at', 'last_failed_login_ip',
        'last_password_reset_at', 'last_profile_update_at',
        'semester', 'portfolio_url', 'social_media', 'skills',
    ];

    protected $hidden = [
        'password', 'remember_token', 'pin', 'qr_token',
    ];

    /**
     * ⚠️ PENTING: `$appends` sebelumnya berisi ['rank', 'total_points'].
     * Ini menyebabkan N+1 query — SETIAP load User menjalankan 2 SUM query.
     * Untuk list 100 user = 200 query tambahan.
     *
     * Sekarang DIKOSONGKAN. Kalau butuh, panggil eksplisit:
     *   $user->rank           // tetap bisa
     *   $user->total_points   // tetap bisa
     *
     * Atau via query builder dengan withSum:
     *   User::withSum('pointHistories as total_points', 'points')->get()
     */
    protected $appends = [];

    protected function casts(): array
    {
        return [
            'password'                       => 'hashed',
            'pin'                            => 'hashed',
            'email_verified_at'              => 'datetime',
            'verified_at'                    => 'datetime',
            'last_login_at'                  => 'datetime',
            'last_seen_at'                   => 'datetime',
            'password_changed_at'            => 'datetime',
            'last_password_confirmed_at'     => 'datetime',
            'avatar_updated_at'              => 'datetime',
            'last_password_reset_at'         => 'datetime',
            'last_profile_update_at'         => 'datetime',
            'last_failed_login_at'           => 'datetime',
            'locked_until'                   => 'datetime',
            'birth_date'                     => 'date',
            'preferences'                    => 'array',
            'settings'                       => 'array',
            'portfolio_links'                => 'array',
            'login_devices'                  => 'array',
            'social_media'                   => 'array',
            'skills'                         => 'array',
            'is_verified'                    => 'boolean',
            'is_active'                      => 'boolean',
            'is_online'                      => 'boolean',
            'receive_email_notification'     => 'boolean',
            'receive_whatsapp_notification'  => 'boolean',
        ];
    }

    /* ═══════════════════════════════════════════════════════
     | LIFECYCLE
     | Guard "ketua hanya 1 global" DIHAPUS.
     | Aturan multi-ketua dipindah ke OrganizationLeadershipService,
     | dengan scope per-organisasi.
     * ═══════════════════════════════════════════════════════ */

    /**
     * @deprecated Dipertahankan sementara untuk kompatibilitas.
     *             Jangan diandalkan untuk multi-organisasi.
     */
    public bool $_bypass_ketua_guard = false;

    /* ═══════════════════════════════════════════════════════
     | PIN — verifikasi keamanan
     * ═══════════════════════════════════════════════════════ */

    /**
     * Default PIN untuk user yang BELUM set PIN sendiri.
     *
     * ⚠️ SECURITY NOTE:
     *   Semua user yang belum set PIN punya PIN yang sama = '12345678'.
     *   Ini adalah risiko keamanan yang harus dimitigasi di production.
     *
     *   Mitigasi yang direkomendasikan:
     *     - Wajibkan set PIN di onboarding
     *     - Log penggunaan DEFAULT_PIN untuk audit
     *     - Tolak aksi kritis (transfer ketua, ubah nominal) jika pin = default
     */
    public const DEFAULT_PIN = '12345678';

    public function setPinAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['pin'] = null;
            return;
        }
        // Jangan re-hash kalau sudah bcrypt
        if (is_string($value) && str_starts_with($value, '$2y$')) {
            $this->attributes['pin'] = $value;
        } else {
            $this->attributes['pin'] = Hash::make($value);
        }
    }

    /**
     * Verifikasi PIN user.
     *
     * @return bool TRUE jika PIN valid (custom atau default untuk user yang belum set)
     */
    public function verifyPin(?string $pin): bool
    {
        if (empty($pin)) {
            return false;
        }

        if (empty($this->pin)) {
            return hash_equals(self::DEFAULT_PIN, (string) $pin);
        }

        return Hash::check($pin, $this->pin);
    }

    /**
     * Apakah user sudah set PIN sendiri (bukan default)?
     */
    public function hasCustomPin(): bool
    {
        return !empty($this->pin);
    }

    /**
     * Alias — kompatibilitas dengan kode lama yang pakai `hasPin()`.
     */
    public function hasPin(): bool
    {
        return $this->hasCustomPin();
    }

    /* ═══════════════════════════════════════════════════════
     | GENERATOR
     * ═══════════════════════════════════════════════════════ */

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

    /* ═══════════════════════════════════════════════════════
     | SCOPES
     * ═══════════════════════════════════════════════════════ */

    public function scopeActive($query)       { return $query->where('is_active', true); }
    public function scopeVerified($query)     { return $query->where('is_verified', true); }
    public function scopeByLevel($query, int $levelId) { return $query->where('level_id', $levelId); }

    /**
     * Scope: user yang anggota aktif organisasi tertentu.
     */
    public function scopeMemberOf($query, int $organizationId)
    {
        return $query->whereHas('organizationMemberships', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId)
              ->where('membership_status', 'active');
        });
    }

    /* ═══════════════════════════════════════════════════════
     | ROLE HELPERS
     | Catatan: ini cek role GLOBAL (Spatie).
     | Untuk kewenangan per organisasi, gunakan isLeaderOf() atau
     | OrganizationContext::authorize().
     * ═══════════════════════════════════════════════════════ */

    public function isOwner(): bool         { return $this->hasRole('owner')      || (int) $this->level_id === 1; }
    public function isSuperAdmin(): bool    { return $this->hasRole('superadmin') || (int) $this->level_id === 2; }
    public function isKetua(): bool         { return $this->hasRole('ketua'); }
    public function isPengurus(): bool      { return $this->hasRole('pengurus') || $this->hasRole('pengurus inti'); }

    /**
     * Apakah user adalah platform admin (Owner / Superadmin)?
     * Bypass semua cek per-organisasi.
     */
    public function isPlatformAdmin(): bool
    {
        return $this->isOwner() || $this->isSuperAdmin();
    }

    /* ═══════════════════════════════════════════════════════
     | ORGANIZATION HELPERS — multi-organisasi
     * ═══════════════════════════════════════════════════════ */

    /**
     * Apakah user adalah leader (ketua) di organisasi tertentu?
     */
    public function isLeaderOf(int $organizationId): bool
    {
        return $this->organizationMemberships()
            ->where('organization_id', $organizationId)
            ->where('access_role', 'leader')
            ->where('membership_status', 'active')
            ->exists();
    }

    /**
     * Apakah user punya keanggotaan aktif (apa pun role-nya) di organisasi ini?
     */
    public function hasOrganization(int $organizationId): bool
    {
        return $this->organizationMemberships()
            ->where('organization_id', $organizationId)
            ->where('membership_status', 'active')
            ->exists();
    }

    /**
     * Role user di organisasi tertentu (jika ada).
     */
    public function roleIn(int $organizationId): ?string
    {
        return $this->organizationMemberships()
            ->where('organization_id', $organizationId)
            ->where('membership_status', 'active')
            ->value('access_role');
    }

    /* ═══════════════════════════════════════════════════════
     | STATUS HELPERS
     * ═══════════════════════════════════════════════════════ */

    public function isActive(): bool    { return (bool) $this->is_active; }
    public function isOnline(): bool    { return (bool) $this->is_online; }
    public function isVerified(): bool  { return (bool) $this->is_verified; }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->verified_at);
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — CORE
     * ═══════════════════════════════════════════════════════ */

    public function level(): BelongsTo
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

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — MULTI-ORGANIZATION
     * ═══════════════════════════════════════════════════════ */

    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class, 'user_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
                Organization::class,
                'organization_memberships',
                'user_id',
                'organization_id'
            )
            ->withPivot([
                'access_role', 'membership_status', 'position_label',
                'jabatan_id', 'period_id',
                'joined_at', 'left_at',
            ])
            ->withTimestamps();
    }

    public function activeOrganizations(): BelongsToMany
    {
        return $this->organizations()
            ->wherePivot('membership_status', 'active')
            ->where('organizations.status', 'active');
    }

    /**
     * Organisasi yang sedang aktif di session user (helper).
     * Untuk mengambil: auth()->user()->activeOrganization()
     */
    public function activeOrganization(): ?Organization
    {
        return app(\App\Services\OrganizationContext::class)->get();
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — JABATAN & PERIODE
     * ═══════════════════════════════════════════════════════ */

    public function jabatans(): BelongsToMany
    {
        return $this->belongsToMany(Jabatan::class, 'jabatan_user')
            ->withPivot([
                'jabatan_user_uuid',
                'is_primary', 'status', 'organizational_period_id',
                'name_user', 'nim_user',
                'start_date', 'end_date',
                'appointment_number', 'appointment_document_path',
                'end_reason',
                'approved_at', 'approved_by', 'approval_notes',
                'revoked_at', 'revoked_by',
                'created_by', 'updated_by',
                'notes', 'settings',
            ])
            ->withTimestamps();
    }

    /**
     * Jabatan aktif (belum berakhir).
     */
    public function activeJabatans(): BelongsToMany
    {
        return $this->jabatans()
            ->wherePivot('status', 'active')
            ->wherePivotNull('end_date')
            ->orWhere(function ($q) {
                $q->wherePivot('status', 'active')
                  ->wherePivot('end_date', '>=', now());
            });
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

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — QR, NFC, LOGIN
     * ═══════════════════════════════════════════════════════ */

    public function qrCode(): HasOne
    {
        return $this->hasOne(UserQrCode::class);
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(UserQrCode::class, 'user_id');
    }

    public function recoverySnapshot(): HasOne
    {
        return $this->hasOne(UserQrRecoverySnapshot::class, 'user_uuid', 'user_uuid');
    }

    public function nfcCredential(): HasOne
    {
        return $this->hasOne(NfcCredential::class)->latestOfMany();
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — POIN & RANK
     * ═══════════════════════════════════════════════════════ */

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

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — EVENTS, PRESENSI, SERTIFIKAT
     * ═══════════════════════════════════════════════════════ */

    public function events(): HasMany        { return $this->hasMany(EventRegistration::class); }
    public function presensis(): HasMany     { return $this->hasMany(Presensi::class); }
    public function sertifikats(): HasMany   { return $this->hasMany(Sertifikat::class); }
    public function pointActivities(): HasMany { return $this->hasMany(PointActivity::class); }
    public function invoices(): HasMany      { return $this->hasMany(\App\Models\Financial\Invoice::class); }

    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_user')
            ->withPivot('created_at')
            ->withTimestamps();
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — CHAT & NOTIFICATION
     * ═══════════════════════════════════════════════════════ */

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('role', 'joined_at', 'left_at', 'last_read_at', 'is_muted')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function createdConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->whereNull('read_at');
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — GROUPS & DIVISIONS
     * ═══════════════════════════════════════════════════════ */

    public function groupAnnouncements(): HasMany
    {
        return $this->hasMany(GroupAnnouncement::class, 'created_by');
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot([
                'position', 'status', 'joined_at', 'left_at',
                'can_view', 'can_announcement', 'can_message', 'can_manage_members',
            ])
            ->withTimestamps();
    }

    public function activeGroups(): BelongsToMany
    {
        return $this->groups()
            ->wherePivot('status', 'aktif')
            ->where('groups.status', 'aktif');
    }

    public function divisionMembers(): HasMany
    {
        return $this->hasMany(DivisionMember::class);
    }

    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'division_members')
            ->withPivot('position', 'status', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — ATTENDANCE
     * ═══════════════════════════════════════════════════════ */

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function attendanceTargets(): HasMany
    {
        return $this->hasMany(AttendanceTarget::class);
    }

    public function attendancePermissions(): HasMany
    {
        return $this->hasMany(\App\Models\AttendancePermission::class);
    }

    public function attendancePointLedgers(): HasMany
    {
        return $this->hasMany(AttendancePointLedger::class);
    }

    public function attendanceSessionsCreated(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'created_by');
    }

    /* ═══════════════════════════════════════════════════════
     | RELATIONSHIPS — GAME
     * ═══════════════════════════════════════════════════════ */

    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    /* ═══════════════════════════════════════════════════════
     | ACCESSORS — POIN, RANK, ATTENDANCE
     * ═══════════════════════════════════════════════════════ */

    /**
     * Total poin approved user.
     *
     * ⚠️ Ini menjalankan query. Untuk list user, gunakan:
     *    User::withSum('pointHistories as total_points', 'points')
     *    User::withSum(['pointHistories as total_points' => fn($q) => $q->where('status','approved')], 'points')
     */
    public function getTotalPointsAttribute(): int
    {
        return (int) $this->pointHistories()
            ->where('status', 'approved')
            ->sum('points');
    }

    /**
     * Rank berdasarkan total poin.
     */
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

    public function getLevelNameAttribute(): string
    {
        return $this->level?->name ?? 'guest';
    }

    public function getAttendancePointsAttribute(): int
    {
        return (int) $this->attendancePointLedgers()->sum('points');
    }

    public function getAttendanceStatsAttribute(): array
    {
        $records = $this->attendanceRecords()
            ->whereIn('status', ['present', 'late', 'late_excused', 'excused_absent', 'absent', 'absent_excused'])
            ->get();

        $total = max(1, $records->count());
        return [
            'total' => $records->count(),
            'hadir' => $records->whereIn('status', ['present', 'late', 'late_excused'])->count(),
            'tepat' => $records->where('status', 'present')->count(),
            'telat' => $records->whereIn('status', ['late', 'late_excused'])->count(),
            'izin'  => $records->whereIn('status', ['excused_absent', 'absent_excused'])->count(),
            'bolos' => $records->where('status', 'absent')->count(),
            'rate'  => round(($records->whereIn('status', ['present', 'late', 'late_excused'])->count() / $total) * 100, 1),
        ];
    }

    /* ═══════════════════════════════════════════════════════
     | ROUTING — dashboard & layout
     * ═══════════════════════════════════════════════════════ */

    /**
     * Layout yang dipakai view.
     * Fallback ke 'pengurus' jika layout pj belum tersedia.
     */
    public function getRoleLayout(): string
    {
        return match ((int) $this->level_id) {
            1 => 'owner',
            2 => 'superadmin',
            3 => 'ketua',
            4 => 'pengurus',
            5 => 'anggota',
            6 => 'anggota-umum',
            7 => 'pengurus',   // PJ pakai layout pengurus (layout 'pj' belum ada)
            8 => 'guest',
            default => 'guest',
        };
    }

    /**
     * Route dashboard berdasarkan level.
     *
     * Prioritas:
     *   1. Route spesifik per level
     *   2. Fallback dalam level yang sama (misal pengurus.tugas → pengurus.dashboard)
     *   3. Fallback terakhir: landing
     *
     * ⚠️ Method ini TIDAK BOLEH dideklarasikan dua kali di file ini.
     */
    public function getDashboardRoute(): string
    {
        $map = [
            1 => 'owner.dashboard',
            2 => 'superadmin.dashboard',
            3 => 'ketua.dashboard',
            4 => 'pengurus.tugas',
            5 => 'anggota.dashboard',
            6 => 'anggota-umum.dashboard',
            7 => 'pengurus.tugas',
            8 => 'landing',
        ];

        $routeName = $map[(int) $this->level_id] ?? null;

        // Fallback pengurus/PJ
        if (in_array($routeName, ['pengurus.tugas'], true) && !Route::has($routeName)) {
            $routeName = Route::has('pengurus.dashboard') ? 'pengurus.dashboard' : 'landing';
        }

        // Fallback anggota umum
        if ($routeName === 'anggota-umum.dashboard' && !Route::has($routeName)) {
            $routeName = 'landing';
        }

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        return route('landing');
    }

    /* ═══════════════════════════════════════════════════════
     | ACTIVITY LOG
     * ═══════════════════════════════════════════════════════ */

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
        /**
     * Label role yang DITAMPILKAN ke user.
     *
     * Prioritas:
     *   1. position_label dari organisasi AKTIF (paling spesifik)
     *   2. position_label dari organisasi MANAPUN (kalau cuma 1 org)
     *   3. level.display_name (fallback generik)
     *
     * Contoh hasil:
     *   - "Ketua UKM Pemrograman UNICODE"
     *   - "Ketua HIMA Informatika"
     *   - "Ketua BEM Universitas Bhakti Asih"
     *   - "Pengurus UKM Pemrograman UNICODE"
     *   - "Anggota" (fallback)
     */
    public function getRoleDisplayAttribute(): string
    {
        // Cek apakah user ini adalah milik session (supaya pakai active org)
        $isCurrentUser = auth()->check() && auth()->id() === $this->id;

        // Prioritas 1: organisasi aktif (session)
        if ($isCurrentUser) {
            $activeOrgId = \App\Services\OrganizationContext::id();
            if ($activeOrgId) {
                $label = $this->organizationMemberships()
                    ->where('organization_id', $activeOrgId)
                    ->where('membership_status', 'active')
                    ->whereNotNull('position_label')
                    ->value('position_label');

                if ($label) return $label;
            }
        }

        // Prioritas 2: organisasi manapun (kalau hanya 1 membership aktif)
        $label = $this->organizationMemberships()
            ->where('membership_status', 'active')
            ->whereNotNull('position_label')
            ->value('position_label');

        if ($label) return $label;

        // Prioritas 3: fallback level display_name
        return $this->level?->display_name ?? '-';
    }

    /**
     * Versi ringkas untuk tampilan list (tidak query per-baris).
     * Gunakan setelah eager load `activeOrganizations`.
     *
     *   User::with(['level', 'activeOrganizations'])->get()
     *   → tiap user bisa panggil `$user->role_display_eager`
     */
    public function getRoleDisplayEagerAttribute(): string
    {
        // Cek relasi yang sudah di-eager-load
        if ($this->relationLoaded('activeOrganizations')) {
            $org = $this->activeOrganizations
                ->firstWhere('pivot.position_label', '!=', null);
            if ($org && $org->pivot->position_label) {
                return $org->pivot->position_label;
            }
        }

        return $this->level?->display_name ?? '-';
    }
}