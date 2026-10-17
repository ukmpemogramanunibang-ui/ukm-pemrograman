<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Impersonation\ImpersonationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Middleware ini:
 *  1. Cek apakah request ini dari Owner yang sedang "menjelajah"
 *  2. Validasi token → set Auth::user() = target user (untuk request ini saja)
 *  3. Mark presence (untuk radar)
 *  4. TIDAK menyentuh session Bima (user asli) — mereka punya cookie berbeda
 */
final class IsolateImpersonationSession
{
    public function __construct(
        private readonly ImpersonationService $impersonation,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('impersonation.concurrent.isolate_session')) {
            return $next($request);
        }

        try {
            /* ── 1. Cek session active + token ── */
            if (! $this->impersonation->isActive()) {
                return $next($request);
            }

            $plainToken = $this->impersonation->currentToken();
            if (! $plainToken) return $next($request);

            /* ── 2. Validate token (cached) ── */
            $token = $this->impersonation->validateToken($plainToken, $request);
            if (! $token || ! $token->targetUser) {
                $this->impersonation->endSession();
                return $next($request);
            }

            /* ── 3. Cek route role vs token role (anti-mismatch) ── */
            $routeRole = (string) $request->route('roleSlug', '');
            if ($routeRole !== '' && $routeRole !== (string) $token->role_slug) {
                Log::warning('impersonation.role_mismatch', [
                    'route'  => $routeRole,
                    'token'  => $token->role_slug,
                    'token_id' => $token->id,
                ]);
                abort(404, 'Token tidak sesuai dengan role yang diminta.');
            }

            /* ── 4. Override Auth::user() untuk request ini SAJA ── */
            // NOTE: SessionGuard::setUser() TIDAK persist ke session — aman.
            Auth::setUser($token->targetUser);

            /* ── 5. Attach ke request attributes (untuk controller) ── */
            $request->attributes->set('impersonation_target', $token->targetUser);
            $request->attributes->set('impersonation_owner',  $token->owner);
            $request->attributes->set('impersonation_token',  $token);

            /* ── 6. Mark presence (radar) ── */
            $this->impersonation->markPresence($token->targetUser->id);

        } catch (Throwable $e) {
            Log::error('impersonation.middleware.failed', [
                'error' => $e->getMessage(),
                'url'   => $request->fullUrl(),
            ]);
            // Jangan crash — fallback ke request normal
        }

        return $next($request);
    }
}