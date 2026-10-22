<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ImpersonationToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

/**
 * RequireImpersonation
 * ============================================================
 * Middleware untuk route /owner/{roleSlug}/{token}/*.
 *  - Validasi format slug & token
 *  - Validasi token di DB (cached)
 *  - Cek kadaluarsa & revoked
 *  - Cek role slug cocok (mendukung slug dengan tanda hubung)
 *  - Bind target user ke request attribute
 *  - Share ke view untuk layout
 *
 * FIX 2026-10-04:
 *   - Regex lama `/^[a-z]{4,32}$/` menolak slug dengan `-` (anggota-ukm)
 *   - Sekarang: `/^[a-z][a-z0-9\-]{2,31}$/`
 */
class RequireImpersonation
{
    public function handle(Request $request, Closure $next): Response
    {
        $roleSlug = (string) $request->route('roleSlug');
        $tokenStr = (string) $request->route('impersonationToken');

        // ── 1. Sanity check format (FIX: izinkan tanda hubung)
        if (! preg_match('/^[a-z][a-z0-9\-]{2,31}$/', $roleSlug)
            || strlen($tokenStr) !== 48) {
            $this->log('format_invalid', $request, $roleSlug, $tokenStr);
            abort(404);
        }

        // ── 2. Rate-limit per token (anti brute force)
        $rlKey = 'impersonate:' . hash('sha256', $tokenStr);
        if (RateLimiter::tooManyAttempts($rlKey, 120)) {
            abort(429, 'Terlalu banyak permintaan. Coba lagi nanti.');
        }
        RateLimiter::hit($rlKey, 60);

        // ── 3. Cari token valid (dari DB + cache)
        $token = ImpersonationToken::findValid($tokenStr);

        if (! $token) {
            $this->log('invalid_token', $request, $roleSlug, $tokenStr);
            abort(403, 'Token jelajah tidak valid atau sudah kadaluarsa.');
        }

        // ── 4. Cek role slug cocok (keduanya boleh mengandung '-')
        if ($token->role_slug !== $roleSlug) {
            $this->log('role_mismatch', $request, $roleSlug, $tokenStr, [
                'token_role' => $token->role_slug,
            ]);
            abort(403, 'Role tidak cocok dengan token.');
        }

        // ── 5. Cek owner masih terautentikasi
        $currentUserId = (int) auth()->id();
        if ($currentUserId !== (int) $token->owner_id) {
            $this->log('owner_mismatch', $request, $roleSlug, $tokenStr, [
                'expected_owner' => $token->owner_id,
                'current_user'   => $currentUserId,
            ]);
            abort(403, 'Sesi jelajah bukan milik akun Anda.');
        }

        // ── 6. Ambil target
        $target = $token->target;
        if (! $target) {
            $token->revoke('target-missing');
            abort(404, 'Akun target tidak ditemukan.');
        }
        if ((int) $target->level_id === 1) {
            $token->revoke('target-is-owner');
            abort(403, 'Akun Owner tidak dapat dijelajahi.');
        }

        // ── 7. Mark used (sekali saja — idempotent)
        $token->markUsed();

        // ── 8. Bind ke request attribute
        $request->attributes->set('impersonation_token',  $token);
        $request->attributes->set('impersonation_target', $target);
        $request->attributes->set('impersonation_role',   $roleSlug);

        // ── 9. Share ke view
        view()->share('exploreTarget',      $target);
        view()->share('exploreRole',        $roleSlug);
        view()->share('isImpersonating',    true);
        view()->share('impersonationToken', $token);

        return $next($request);
    }

    protected function log(
        string $event,
        Request $r,
        string $roleSlug,
        string $token,
        array $extra = []
    ): void {
        try {
            \Log::warning("[Impersonation] {$event}", array_merge($extra, [
                'role_slug' => $roleSlug,
                'token'     => substr($token, 0, 8) . '…',
                'owner_id'  => auth()->id(),
                'ip'        => $r->ip(),
                'url'       => $r->fullUrl(),
            ]));
        } catch (\Throwable $e) {}
    }
}