<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * ImpersonationGuard
 * ============================================================
 * Menjaga konsistensi state "mode jelajah" (impersonation).
 *
 * FILOSOFI:
 *   - Route `owner.impersonate.*` adalah SOURCE OF TRUTH (token di URL).
 *     Jangan pernah redirect dari sini.
 *   - Session `owner.explore.*` hanya untuk UI hints (banner, dropdown).
 *   - Kalau session bertentangan dengan URL → URL yang menang.
 *
 * ANTI-LOOP:
 *   - Kirim header `X-Impersonate-Hop` untuk deteksi redirect berulang.
 *   - Batas maksimal 3 hop sebelum menolak request.
 *
 * ANTI-RACE:
 *   - Cek `auth()->id()` vs `session('owner.explore.owner_id')`.
 *   - Jangan hapus session kalau user tidak berubah.
 */
class ImpersonationGuard
{
    /** Batas maksimum redirect berulang sebelum dianggap loop. */
    private const MAX_HOPS = 3;

    public function handle(Request $request, Closure $next): Response
    {
        $userId = (int) (auth()->id() ?? 0);
    $isExploring = (bool) session('owner.explore.active', false);
    $impersonatorId = (int) session('impersonator_id', 0);

    if ($isExploring && (
            (auth()->check() && (int) auth()->user()->level_id === 1 && $userId === (int) session('owner.explore.owner_id', 0))
            || ($impersonatorId > 0 && $impersonatorId === (int) session('owner.explore.owner_id', 0))
        )) {
        return $next($request);
    }
        // ─────────────────────────────────────────────────────────
        // 1. ANTI-LOOP: cek hop counter
        // ─────────────────────────────────────────────────────────
        $hop = (int) $request->header('X-Impersonate-Hop', '0');
        if ($hop > self::MAX_HOPS) {
            $this->log('hop_limit_exceeded', $request, ['hop' => $hop]);
            return response('Terlalu banyak redirect. Silakan muat ulang.', 508);
        }
    // ✨ Owner yang sedang explore = bypass total
    $currentUserId = (int) (auth()->id() ?? 0);
    if ((bool) session('owner.explore.active', false)
        && $currentUserId === (int) session('owner.explore.owner_id', 0)
        && auth()->check()
        && (int) auth()->user()->level_id === 1) {
        return $next($request);
    }
        // ─────────────────────────────────────────────────────────
        // 2. PRIORITAS: route impersonate → langsung lanjut
        //    (jangan sentuh session, biar RequireImpersonation yang handle)
        // ─────────────────────────────────────────────────────────
        if ($request->routeIs('owner.impersonate.*')) {
            return $next($request)->withHeaders([
                'X-Impersonate-Hop' => $hop + 1,
            ]);
        }

        // ─────────────────────────────────────────────────────────
        // 3. Kalau bukan route impersonate, cek konsistensi session
        // ─────────────────────────────────────────────────────────
        try {
            $this->reconcileExploreSession($request);
        } catch (\Throwable $e) {
            // Fail-safe: log, jangan crash
            $this->log('reconcile_failed', $request, [
                'error' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }

protected function reconcileExploreSession(Request $request): void
{
    // Skip: request AJAX/polling
    if ($request->ajax() || $request->expectsJson()) {
        return;
    }

    $isExploring = (bool) session('owner.explore.active', false);
    if (! $isExploring) {
        return;
    }

    $ownerId        = (int) session('owner.explore.owner_id', 0);
    $currentId      = (int) (Auth::id() ?? 0);
    $impersonatorId = (int) session('impersonator_id', 0);

    /* ═══════════════════════════════════════════════════════
     | ✨ FIX: KALAU SEDANG IMPERSONATE (auth_id = target user)
     | JANGAN clear session — biarkan explore tetap berjalan!
     * ═══════════════════════════════════════════════════════ */
    if ($impersonatorId > 0 && $impersonatorId === $ownerId) {
        // Cukup verifikasi required keys ada, tanpa clear
        $token    = (string) session('owner.explore.token', '');
        $roleSlug = (string) session('owner.explore.role_slug', '');
        $userId   = (int) session('owner.explore.user_id', 0);

        if ($token === '' || $roleSlug === '' || $userId === 0) {
            $this->clearExploreSession('incomplete_data_impersonating', $request);
        }
        return; // ⬅️ KELUAR — jangan lanjut ke check owner_changed
    }

    // Skenario A: user logout → bersihkan
    if ($currentId === 0) {
        $this->clearExploreSession('user_logout', $request);
        return;
    }

    // Skenario B: user ganti (login akun lain, bukan impersonasi)
    if ($ownerId > 0 && $currentId !== $ownerId && $impersonatorId === 0) {
        $this->clearExploreSession('owner_changed', $request, [
            'old_owner' => $ownerId,
            'new_owner' => $currentId,
        ]);
        return;
    }

    // Skenario C: session explore tanpa data wajib
    $token    = (string) session('owner.explore.token', '');
    $roleSlug = (string) session('owner.explore.role_slug', '');
    $userId   = (int) session('owner.explore.user_id', 0);

    if ($token === '' || $roleSlug === '' || $userId === 0) {
        $this->clearExploreSession('incomplete_data', $request);
    }
}

    /**
     * Bersihkan semua key session explore.
     */
    protected function clearExploreSession(string $reason, Request $request, array $ctx = []): void
    {
        session()->forget([
            'owner.explore.active',
            'owner.explore.owner_id',
            'owner.explore.user_id',
            'owner.explore.role_slug',
            'owner.explore.token',
            'owner.explore.token_id',
            'owner.explore.started_at',
            'owner.explore.last_activity_at',
        ]);

        $this->log('explore_cleared', $request, array_merge($ctx, ['reason' => $reason]));
    }

    /**
     * Log terstruktur untuk audit forensik.
     */
    protected function log(string $event, Request $request, array $ctx = []): void
    {
        try {
            Log::warning("[ImpersonationGuard] {$event}", array_merge($ctx, [
                'ip'         => $request->ip(),
                'url'        => $request->fullUrl(),
                'route'      => $request->route()?->getName(),
                'user_id'    => Auth::id(),
                'ua'         => substr((string) $request->userAgent(), 0, 200),
                'timestamp'  => now()->toIso8601String(),
            ]));
        } catch (\Throwable $e) {
            // silent — never crash on logging
        }
    }
}