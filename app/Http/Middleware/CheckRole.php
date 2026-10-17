<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    private const LEVEL_SLUG = [
        1 => 'owner',
        2 => 'superadmin',
        3 => 'ketua',
        4 => 'pengurus',
        5 => 'anggota',
        6 => 'pengunjung',
    ];

    private const ALIASES = [
        'guest'       => ['pengunjung', 'guest'],
        'pengunjung'  => ['pengunjung', 'guest'],
        'guest_umum'  => ['anggota-umum', 'pengunjung', 'guest'],
        'anggota_ukm' => ['anggota', 'anggota-ukm'],
        'anggota'     => ['anggota', 'anggota-ukm'],
    ];

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        /* ═══════════════════════════════════════════════════
         | 0. ✨ OWNER EXPLORE BYPASS — FIX UTAMA
         | Owner yang sedang explore → boleh akses SEMUA route
         * ═══════════════════════════════════════════════════ */
        if ($this->isOwnerExploring($user)) {
            Log::debug('[CheckRole] Owner explore bypass', [
                'user_id'   => $user->id,
                'route'     => $request->path(),
                'role_slug' => session('owner.explore.role_slug'),
                'requested' => $roles,
            ]);
            return $next($request);
        }

        /* ═══════════════════════════════════════════════════
         | 1. LEGACY IMPERSONATION (session lama)
         * ═══════════════════════════════════════════════════ */
        $imp = Session::get('impersonation_current');
        if (is_array($imp) && ! empty($imp['role_slug'])) {
            if (in_array($imp['role_slug'], $roles, true)) {
                return $next($request);
            }
        }

        /* ═══════════════════════════════════════════════════
         | 2. ROLE CHECK — Dual mode (Spatie + level_id)
         * ═══════════════════════════════════════════════════ */
        foreach ($roles as $role) {
            if ($this->userHasRole($user, $role)) {
                return $next($request);
            }
        }

        /* ═══════════════════════════════════════════════════
         | 3. REDIRECT CERDAS
         * ═══════════════════════════════════════════════════ */

        // Kalau legacy impersonation tapi role tidak cocok
        if (is_array($imp) && ! empty($imp['role_slug']) && ! empty($imp['token'])) {
            if (Route::has('owner.impersonate.dashboard')) {
                return redirect()
                    ->route('owner.impersonate.dashboard', [
                        'roleSlug'           => $imp['role_slug'],
                        'impersonationToken' => $imp['token'],
                    ])
                    ->with('warning', 'Halaman ini tidak tersedia untuk akun yang sedang dijelajahi.');
            }
        }

        // Redirect berdasarkan level_id user asli
        $target = $this->dashboardRouteForUser($user);
        if ($target) {
            return redirect($target)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return redirect(Route::has('landing') ? route('landing') : '/');
    }

    /* ═══════════════════════════════════════════════════════════
     | HELPERS
     * ═══════════════════════════════════════════════════════════ */

private function isOwnerExploring($user): bool
{
    if (! $user) return false;

    $sessionFlag = (bool) session('owner.explore.active', false);
    if (! $sessionFlag) return false;

    $sessionOwner = (int) session('owner.explore.owner_id', 0);
    $roleSlug     = session('owner.explore.role_slug');
    $token        = session('owner.explore.token');
    $exploreUserId = session('owner.explore.user_id');

    if (empty($roleSlug) || empty($token) || empty($exploreUserId)) {
        return false;
    }

    /* ═══════════════════════════════════════════════════════
     | CASE 1: Direct owner (belum impersonate, auth_id = owner_id)
     * ═══════════════════════════════════════════════════════ */
    if ((int) $user->level_id === 1 && (int) $user->id === $sessionOwner) {
        return true;
    }

    /* ═══════════════════════════════════════════════════════
     | CASE 2: Sedang impersonate (auth user = target)
     | Cek via `impersonator_id` session key
     * ═══════════════════════════════════════════════════════ */
    $impersonatorId = (int) session('impersonator_id', 0);
    if ($impersonatorId > 0 && $impersonatorId === $sessionOwner) {
        $ownerUser = \App\Models\User::find($impersonatorId);
        if ($ownerUser && (int) $ownerUser->level_id === 1) {
            return true;
        }
    }

    return false;
}

    private function userHasRole($user, string $role): bool
    {
        try {
            if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
                return true;
            }
        } catch (\Throwable $e) {}

        $levelSlug = self::LEVEL_SLUG[(int) $user->level_id] ?? null;
        if ($levelSlug === $role) return true;

        if (isset(self::ALIASES[$role])) {
            foreach (self::ALIASES[$role] as $alias) {
                if ($alias === $levelSlug) return true;
            }
        }
        if ($levelSlug && isset(self::ALIASES[$levelSlug])) {
            foreach (self::ALIASES[$levelSlug] as $alias) {
                if ($alias === $role) return true;
            }
        }
        return false;
    }

    private function dashboardRouteForUser($user): ?string
    {
        $map = [
            1 => 'owner.dashboard',
            2 => 'superadmin.dashboard',
            3 => 'ketua.dashboard',
            4 => 'pengurus.tugas',
            5 => 'anggota.dashboard',
            6 => 'explore',
        ];

        $routeName = $map[(int) $user->level_id] ?? null;
        if (! $routeName) return null;

        if ($routeName === 'pengurus.tugas' && ! Route::has('pengurus.tugas')) {
            $routeName = Route::has('pengurus.dashboard') ? 'pengurus.dashboard' : null;
        }

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }
        return null;
    }
}