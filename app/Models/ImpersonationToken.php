<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ImpersonationToken
 * ============================================================
 * Menyimpan sesi "mode jelajah" Owner.
 *  - Satu token = satu sesi tab browser
 *  - Maksimal 7 sesi aktif per Owner
 *  - Auto-revoke sesi tertua kalau limit terlampaui
 *  - Token 48 char URL-safe
 *  - TTL default 8 jam (configurable)
 */
class ImpersonationToken extends Model
{
    /** Batas maksimal sesi explore per Owner. */
    public const MAX_SESSIONS = 7;

    /** Default masa aktif token (jam). */
    public const DEFAULT_TTL_HOURS = 8;

    /** Prefix cache untuk validasi token. */
    public const CACHE_PREFIX = 'impersonation:token:';

    protected $table = 'impersonation_tokens';

    protected $fillable = [
        'token', 'owner_id', 'target_user_id', 'role_slug',
        'ip_address', 'user_agent', 'used_at', 'expires_at', 'revoked_at',
    ];

    protected $casts = [
        'used_at'    => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    protected $hidden = [
        'token', // jangan pernah expose token di JSON
    ];

    /* =========================================================
     | BOOT — Auto-generate token & auto-cleanup
     ========================================================= */

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->token)) {
                $model->token = self::generateUniqueToken();
            }
            if (empty($model->expires_at)) {
                $model->expires_at = now()->addHours(self::DEFAULT_TTL_HOURS);
            }
        });

        // Setiap model di-revoke → lupakan cache
        static::updated(function (self $model) {
            if ($model->isDirty('revoked_at') && $model->revoked_at) {
                self::forgetCache($model->token);
            }
        });

        static::deleted(function (self $model) {
            self::forgetCache($model->token);
        });
    }

    /* =========================================================
     | RELATIONS
     ========================================================= */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /* =========================================================
     | SCOPES
     ========================================================= */

    public function scopeActive(Builder $q): Builder
    {
        return $q->whereNull('revoked_at')
                 ->where(function ($w) {
                     $w->whereNull('expires_at')
                       ->orWhere('expires_at', '>', now());
                 });
    }

    public function scopeExpired(Builder $q): Builder
    {
        return $q->whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    public function scopeRevoked(Builder $q): Builder
    {
        return $q->whereNotNull('revoked_at');
    }

    public function scopeForOwner(Builder $q, int $ownerId): Builder
    {
        return $q->where('owner_id', $ownerId);
    }

    public function scopeForTarget(Builder $q, int $targetId): Builder
    {
        return $q->where('target_user_id', $targetId);
    }

    /* =========================================================
     | HELPERS
     ========================================================= */

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isActive(): bool
    {
        return ! $this->isExpired() && ! $this->isRevoked();
    }

    /** Alias lama, tetap kompatibel. */
    public function isValid(): bool
    {
        return $this->isActive();
    }

    public function markUsed(): void
    {
        if (! $this->used_at) {
            $this->forceFill(['used_at' => now()])->saveQuietly();
        }
    }

    public function revoke(?string $reason = null): bool
    {
        if ($this->isRevoked()) return false;

        $this->forceFill([
            'revoked_at' => now(),
            'user_agent' => $reason
                ? substr((string) $this->user_agent . ' | revoked: ' . $reason, 0, 500)
                : $this->user_agent,
        ])->save();

        self::forgetCache($this->token);
        return true;
    }

    /** Sisa waktu dalam detik. */
    public function remainingSeconds(): int
    {
        if (! $this->expires_at) return 0;
        return max(0, (int) now()->diffInSeconds($this->expires_at, false));
    }

    /* =========================================================
     | STATIC — Generate & Manage
     ========================================================= */

    /**
     * Buat token unik 48 char yang belum dipakai.
     */
    protected static function generateUniqueToken(): string
    {
        $attempts = 0;
        do {
            $token = Str::random(48);
            $exists = static::where('token', $token)->exists();
            $attempts++;
        } while ($exists && $attempts < 5);

        return $token;
    }
public function targetUser()
{
    // Sesuaikan nama kolom dengan migration kamu
    // Coba salah satu:
    return $this->belongsTo(\App\Models\User::class, 'target_user_id');
    // return $this->belongsTo(\App\Models\User::class, 'user_id');
    // return $this->belongsTo(\App\Models\User::class, 'impersonated_user_id');
}

public function ownerUser()
{
    return $this->belongsTo(\App\Models\User::class, 'owner_id');
}
    /**
     * Generate token baru (dengan enforcement limit 7 sesi).
     * Reuse token kalau target sama & masih aktif.
     */
    public static function generate(
        int $ownerId,
        int $targetUserId,
        string $roleSlug,
        ?string $ip = null,
        ?string $ua = null
    ): self {
        return DB::transaction(function () use ($ownerId, $targetUserId, $roleSlug, $ip, $ua) {
            // 1. Cleanup expired tokens owner ini
            static::cleanupExpired($ownerId);

            // 2. Cek apakah sudah ada sesi aktif untuk target yang sama
            $existing = static::active()
                ->forOwner($ownerId)
                ->forTarget($targetUserId)
                ->first();

            if ($existing) {
                // Refresh token lama
                $existing->forceFill([
                    'role_slug'  => $roleSlug,
                    'ip_address' => $ip,
                    'user_agent' => $ua ? substr($ua, 0, 500) : $existing->user_agent,
                    'expires_at' => now()->addHours(self::DEFAULT_TTL_HOURS),
                ])->save();

                return $existing;
            }

            // 3. Enforce limit — kalau >= MAX_SESSIONS, revoke oldest
            $activeCount = static::active()->forOwner($ownerId)->count();
            if ($activeCount >= self::MAX_SESSIONS) {
                $excess = $activeCount - self::MAX_SESSIONS + 1;
                static::active()
                    ->forOwner($ownerId)
                    ->orderBy('created_at')
                    ->limit($excess)
                    ->get()
                    ->each(fn (self $t) => $t->revoke('auto-limit'));
            }

            // 4. Buat token baru
            return static::create([
                'owner_id'       => $ownerId,
                'target_user_id' => $targetUserId,
                'role_slug'      => $roleSlug,
                'ip_address'     => $ip,
                'user_agent'     => $ua ? substr($ua, 0, 500) : null,
                'expires_at'     => now()->addHours(self::DEFAULT_TTL_HOURS),
            ]);
        });
    }

    /**
     * Hapus token yang sudah expired (soft delete via revoke).
     */
    public static function cleanupExpired(?int $ownerId = null): int
    {
        $query = static::expired()->whereNull('revoked_at');
        if ($ownerId !== null) {
            $query->forOwner($ownerId);
        }

        return $query->update(['revoked_at' => now()]);
    }

    /**
     * Ambil semua sesi aktif Owner.
     */
    public static function activeSessionsFor(int $ownerId)
    {
        return static::with('target:id,name,level_id')
            ->active()
            ->forOwner($ownerId)
            ->orderByDesc('created_at')
            ->limit(self::MAX_SESSIONS)
            ->get();
    }

    /**
     * Cari token valid dari string (dengan cache).
     */
    public static function findValid(string $token): ?self
    {
        if (strlen($token) !== 48) return null;

        $cacheKey = self::CACHE_PREFIX . hash('sha256', $token);

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($token) {
            $model = static::where('token', $token)->first();
            return $model && $model->isActive() ? $model : null;
        });
    }

    public static function forgetCache(string $token): void
    {
        try {
            cache()->forget(self::CACHE_PREFIX . hash('sha256', $token));
        } catch (\Throwable $e) {}
    }

    /* =========================================================
     | ACCESSORS
     ========================================================= */

    public function getIsActiveAttribute(): bool
    {
        return $this->isActive();
    }

    public function getRemainingHumanAttribute(): string
    {
        $sec = $this->remainingSeconds();
        if ($sec <= 0) return 'expired';
        if ($sec < 60) return $sec . ' detik';
        if ($sec < 3600) return floor($sec / 60) . ' menit';
        return floor($sec / 3600) . ' jam';
    }
}