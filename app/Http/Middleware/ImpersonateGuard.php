<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

class ImpersonateGuard
{
    /**
     * Key session yang menandakan owner sedang menjelajah.
     */
    protected function resolveTargetId(): ?int
    {
        return session('impersonated_user_id')
            ?? session('impersonate_user_id')
            ?? session('explore_user_id')
            ?? session('target_user_id');
    }

    protected function resolveRoleSlug(): string
    {
        return session('impersonate_role')
            ?? session('explore_role')
            ?? session('role_slug')
            ?? 'anggota';
    }

    /**
     * Bersihkan SEMUA state impersonation.
     */
    protected function purgeImpersonation(): void
    {
        session()->forget([
            'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
            'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
            'target_user_id', 'role_slug', 'impersonator_id',
            'impersonate_role', 'explore_role',
        ]);
        View::share('isImpersonating', false);
    }

    public function handle(Request $request, Closure $next)
    {
        /* ── 1. Route publik penting: lewati total ── */
        if ($request->is(
            'logout',
            'panic-exit',
            'exit-impersonation',
            'explore/reset',
            'login',
            'login/*',
            'check-auth-status',
            'owner/settings/explore/*',
            '_debugbar/*',
            'livewire/*',
        )) {
            return $next($request);
        }

        /* ── 2. Cek apakah ada target impersonation ── */
        $targetId = $this->resolveTargetId();

        if (! $targetId) {
            return $next($request);
        }

        /* ── 3. VALIDASI KETAT: cuma owner asli yang boleh pakai mode ini ── */
        $impersonatorId = session('impersonator_id');

        if (! $impersonatorId) {
            // Tidak ada jejak owner → jangan hijack, bersihkan & lanjut
            $this->purgeImpersonation();
            return $next($request);
        }

        $impersonator = User::find($impersonatorId);
        if (! $impersonator || (int) $impersonator->level_id !== 1) {
            // Bukan owner → tolak & bersihkan
            $this->purgeImpersonation();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi explorer tidak valid.'], 403);
            }
            return redirect('/login')->with('error', 'Sesi explorer tidak valid. Silakan login ulang.');
        }

        /* ── 4. Target harus ada ── */
        $target = User::find($targetId);
        if (! $target) {
            $this->purgeImpersonation();
            return $next($request);
        }

        /* ── 5. Pasang state impersonation ── */
        session([
            'impersonate_active'    => true,
            'impersonate_target_id' => $target->id,
            'impersonate_role_slug' => $this->resolveRoleSlug(),
        ]);

        Auth::setUser($target);
        View::share('isImpersonating', true);
        View::share('impersonatedUser', $target);

        /* ── 6. Redirect route anggota.* → owner.impersonate.* ── */
        if ($request->isMethod('GET') && ! $request->ajax() && ! $request->wantsJson()) {
            $routeName = $request->route()?->getName() ?? '';
            $roleSlug  = $this->resolveRoleSlug();

            foreach (['anggota.', 'pengurus.', 'ketua.', 'superadmin.', 'pengunjung.'] as $prefix) {
                if (str_starts_with($routeName, $prefix) && ! str_starts_with($routeName, 'owner.')) {
                    $action     = substr($routeName, strlen($prefix));
                    $ownerRoute = 'owner.impersonate.' . $action;
                    if (Route::has($ownerRoute)) {
                        return redirect()->route($ownerRoute, ['roleSlug' => $roleSlug]);
                    }
                }
            }
        }

        return $next($request);
    }
}