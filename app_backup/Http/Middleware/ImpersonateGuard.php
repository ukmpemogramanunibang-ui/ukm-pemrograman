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
     * Deteksi key session yang menandakan owner sedang menjelajah.
     * Sesuaikan daftar ini dengan yang dipakai ExploreAccountController::enter().
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

public function handle(Request $request, Closure $next)
{
    // ⭐ Jangan pernah guard route publik yang penting
    if ($request->is(
        'logout',
        'panic-exit',
        'exit-impersonation',
        'login',
        'login/*',
        'check-auth-status',
        'owner/settings/explore/*',
        '_debugbar/*',
        'livewire/*',
    )) {
        return $next($request);
    }

    $targetId = $this->resolveTargetId();

    if (! $targetId) {
        return $next($request);
    }

    $target = User::find($targetId);
    if (! $target) {
        // Target hilang → bersihkan session, jangan abort
        session()->forget([
            'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
            'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
            'target_user_id', 'role_slug', 'impersonator_id',
        ]);
        return $next($request);
    }

    session([
        'impersonate_active'    => true,
        'impersonate_target_id' => $target->id,
        'impersonate_role_slug' => $this->resolveRoleSlug(),
    ]);

    if (! session()->has('impersonator_id')) {
        session(['impersonator_id' => Auth::id()]);
    }

    Auth::setUser($target);
    View::share('isImpersonating', true);
    View::share('impersonatedUser', $target);

    // Redirect /anggota/... → /owner/anggota/...
    if ($request->isMethod('GET') && ! $request->ajax() && ! $request->wantsJson()) {
        $routeName = $request->route()?->getName() ?? '';
        $roleSlug  = $this->resolveRoleSlug();

        foreach (['anggota.', 'pengurus.', 'ketua.', 'superadmin.', 'pengunjung.'] as $prefix) {
            if (str_starts_with($routeName, $prefix) && ! str_starts_with($routeName, 'owner.')) {
                $action = substr($routeName, strlen($prefix));
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