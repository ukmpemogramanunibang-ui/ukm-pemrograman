<?php

declare(strict_types=1);

namespace App\Services\Impersonation;

use App\Models\ImpersonationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * =============================================================================
 *  ImpersonationService — Nexus Edition
 * =============================================================================
 *
 *  Pilar:
 *   1. Single Source of Truth   — semua role slug & view mapping dari config
 *   2. Concurrent-Safe          — Owner & user asli bisa aktif bersamaan
 *   3. Invisible                — user asli tidak tahu sedang di-explore
 *   4. Fast                     — cache 5 menit, atomic ops, no N+1
 *   5. Anti-Bug                 — try/catch berlapis, idempotent, timeout
 * =============================================================================
 */
final class ImpersonationService
{
    private const CACHE_PREFIX = 'impersonation:v3:';
    private const PRESENCE_KEY = 'presence:';

    /* ═══════════════════════════════════════════════════════════
     |  ROLE RESOLUTION — SINGLE SOURCE OF TRUTH
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Resolve role slug dari user.
     * Mengembalikan 'anggota-ukm' untuk level 5 kecuali user adalah Anggota Umum.
     */
    public function resolveRoleSlug(User $user): string
    {
        $levelId = (int) ($user->level_id ?? 0);

        if ($levelId === 5) {
            $marker = config('impersonation.anggota_umum_marker', []);
            $isUmum = (
                (string) ($user->name ?? '') === ($marker['name'] ?? 'Anggota Umum')
                || (string) ($user->account_type ?? '') === ($marker['account_type'] ?? 'umum')
            );
            return $isUmum ? 'anggota-umum' : 'anggota-ukm';
        }

        return (string) (config('impersonation.roles')[$levelId] ?? 'anggota-ukm');
    }

    public function resolveView(string $roleSlug): ?string
    {
        return config('impersonation.views')[$roleSlug] ?? null;
    }

    /* ═══════════════════════════════════════════════════════════
     |  SESSION — SET / CLEAR
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Set session context untuk tab/browser milik Owner.
     * TIDAK mengganggu session Bima (user asli) karena mereka pakai cookie berbeda.
     */
    public function startSession(User $owner, User $target, ImpersonationToken $token): void
    {
        $keys = config('impersonation.session_keys');

        session([
            $keys['active']         => true,
            $keys['owner_id']       => $owner->id,
            $keys['user_id']        => $target->id,
            $keys['role_slug']      => $token->role_slug,
            $keys['token']          => $token->token,
            $keys['token_id']       => $token->id,
            $keys['started_at']     => now()->toIso8601String(),
            $keys['last_activity']  => now()->toIso8601String(),
            $keys['last_validated'] => now()->timestamp,
        ]);

        $this->markPresence($target->id);
    }

    public function endSession(): void
    {
        $keys = config('impersonation.session_keys');
        foreach ($keys as $key) {
            session()->forget($key);
        }
    }

    public function isActive(): bool
    {
        return (bool) session(config('impersonation.session_keys.active'), false);
    }

    public function currentToken(): ?string
    {
        return session(config('impersonation.session_keys.token'));
    }

    public function currentRoleSlug(): ?string
    {
        return session(config('impersonation.session_keys.role_slug'));
    }

    /* ═══════════════════════════════════════════════════════════
     |  TOKEN VALIDATION — FAST + SAFE
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Validasi token dengan cache.
     * Return ImpersonationToken valid atau null.
     */
    public function validateToken(string $plainToken, Request $request): ?ImpersonationToken
    {
        $cacheKey = self::CACHE_PREFIX . 'token:' . hash('sha256', $plainToken);
        $ttl      = (int) config('impersonation.security.revalidate_every', 300);

        try {
            $cached = Cache::remember($cacheKey, $ttl, function () use ($plainToken) {
                return ImpersonationToken::query()
                    ->where('token', $plainToken)
                    ->with(['owner:id,name,level_id', 'targetUser:id,name,level_id,nim,email,account_type'])
                    ->first();
            });
        } catch (Throwable $e) {
            Log::warning('impersonation.validate_token.failed', ['error' => $e->getMessage()]);
            return null;
        }

        if (! $cached instanceof ImpersonationToken) return null;
        if (! $this->isTokenStillValid($cached, $request)) return null;

        return $cached;
    }

    /**
     * Cek kondisi token masih valid (expired, IP binding, UA binding).
     */
    private function isTokenStillValid(ImpersonationToken $token, Request $request): bool
    {
        /* 1. Expired */
        if ($token->expires_at && $token->expires_at->isPast()) return false;

        /* 2. Revoked */
        if (! empty($token->revoked_at)) return false;

        /* 3. IP binding */
        if (config('impersonation.security.bind_ip') && $token->ip_address) {
            if ((string) $token->ip_address !== (string) $request->ip()) {
                Log::warning('impersonation.token.ip_mismatch', [
                    'token_id' => $token->id,
                    'expected' => $token->ip_address,
                    'actual'   => $request->ip(),
                ]);
                return false;
            }
        }

        /* 4. UA binding — toleran */
        if (config('impersonation.security.bind_user_agent') && $token->user_agent) {
            $tolerance = (int) config('impersonation.security.ua_tolerance_chars', 40);
            $stored = (string) $token->user_agent;
            $actual = (string) $request->userAgent();
            if (abs(strlen($stored) - strlen($actual)) > $tolerance) return false;
            if (substr($stored, 0, 80) !== substr($actual, 0, 80)) return false;
        }

        return true;
    }

    /* ═══════════════════════════════════════════════════════════
     |  PRESENCE RADAR — Concurrent Detection
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Tandai bahwa user sedang aktif (dari sisi user asli).
     * Kita pakai cache dengan TTL pendek, TIDAK mengganggu kolom users.last_seen_at.
     */
    public function markPresence(int $userId): void
    {
        if (! config('impersonation.concurrent.enabled')) return;

        try {
            $window = (int) config('impersonation.concurrent.presence_window_seconds', 120);
            Cache::put(self::PRESENCE_KEY . $userId, now()->timestamp, $window);
        } catch (Throwable) {
            // silent
        }
    }

    /**
     * Cek apakah user asli saat ini sedang aktif di device-nya sendiri.
     * Return: [active: bool, last_seen: int|null, seconds_ago: int|null]
     */
    public function detectRealUserPresence(int $userId): array
    {
        if (! config('impersonation.concurrent.detect_real_user_presence')) {
            return ['active' => false, 'last_seen' => null, 'seconds_ago' => null];
        }

        try {
            $lastSeen = Cache::get(self::PRESENCE_KEY . $userId);
            if (! $lastSeen) {
                return ['active' => false, 'last_seen' => null, 'seconds_ago' => null];
            }
            $secondsAgo = now()->timestamp - (int) $lastSeen;
            $window     = (int) config('impersonation.concurrent.presence_window_seconds', 120);

            return [
                'active'      => $secondsAgo <= $window,
                'last_seen'   => (int) $lastSeen,
                'seconds_ago' => $secondsAgo,
            ];
        } catch (Throwable) {
            return ['active' => false, 'last_seen' => null, 'seconds_ago' => null];
        }
    }

    /* ═══════════════════════════════════════════════════════════
     |  INVISIBLE MODE — Owner tidak muncul di log user asli
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Jalankan callback dengan event Eloquent dimatikan.
     * Berguna agar update `last_seen_at` / notifikasi Bima tidak ter-trigger
     * saat Owner mengakses dashboard Bima.
     */
    public function silently(callable $callback): mixed
    {
        if (! config('impersonation.concurrent.silent_mode')) {
            return $callback();
        }

        try {
            return User::withoutEvents($callback);
        } catch (Throwable $e) {
            Log::warning('impersonation.silent.failed', ['error' => $e->getMessage()]);
            return $callback();
        }
    }

    /* ═══════════════════════════════════════════════════════════
     |  SESSION RADAR — Timeline + Health
     * ═══════════════════════════════════════════════════════════ */

    /**
     * Ambil ringkasan sesi jelajah yang aktif:
     *   - Durasi
     *   - User asli sedang online?
     *   - Jumlah aktivitas Owner
     *   - Sisa waktu token
     */
    public function sessionRadar(int $targetUserId, ?ImpersonationToken $token = null): array
    {
        $startedAt   = session(config('impersonation.session_keys.started_at'));
        $started     = $startedAt ? \Carbon\Carbon::parse($startedAt) : now();
        $duration    = max(0, now()->diffInSeconds($started));
        $presence    = $this->detectRealUserPresence($targetUserId);

        $expiresIn = null;
        if ($token && $token->expires_at) {
            $expiresIn = max(0, $token->expires_at->diffInSeconds(now()));
        }

        return [
            'active'         => $this->isActive(),
            'duration_s'     => $duration,
            'started_at'     => $started->toIso8601String(),
            'real_user'      => $presence,
            'expires_in_s'   => $expiresIn,
            'silent_mode'    => (bool) config('impersonation.concurrent.silent_mode'),
        ];
    }

    /* ═══════════════════════════════════════════════════════════
     |  HELPERS
     * ═══════════════════════════════════════════════════════════ */

    public function resetRateLimiter(int $ownerId): void
    {
        try {
            \Illuminate\Support\Facades\RateLimiter::clear('owner-explore:' . $ownerId);
        } catch (Throwable) {}
    }
}