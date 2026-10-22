<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * GLOBAL SAFETY NET
 * Cek setiap request yang masuk ke path yang mengandung 'owner' atau 'ketua/divisi-kelompok-jabatan-owner'.
 * Blokir jika bukan owner.
 */
class BlockNonOwnerGlobal
{
    /** Path prefix yang dilindungi */
    protected array $protectedPrefixes = [
        'owner',
        'owner/',
    ];

    /** Route name patterns yang dilarang */
    protected array $protectedRoutePatterns = [
        'owner.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $currentUserId = (int) (auth()->id() ?? 0);
    if (auth()->check()
        && (int) auth()->user()->level_id === 1
        && (bool) session('owner.explore.active', false)
        && $currentUserId === (int) session('owner.explore.owner_id', 0)
        && session('owner.explore.role_slug')
        && session('owner.explore.token')) {
        return $next($request);
    }
        $path = trim($request->path(), '/');
        $routeName = $request->route()?->getName() ?? '';

        /* Cek path */
        $pathProtected = false;
        foreach ($this->protectedPrefixes as $prefix) {
            if ($path === rtrim($prefix, '/') || str_starts_with($path, $prefix)) {
                $pathProtected = true;
                break;
            }
        }

        /* Cek route name */
        $routeProtected = false;
        foreach ($this->protectedRoutePatterns as $pattern) {
            if ($routeName && str_starts_with($routeName, rtrim($pattern, '*'))) {
                $routeProtected = true;
                break;
            }
        }

        if (! $pathProtected && ! $routeProtected) {
            return $next($request);
        }

        /* Route ini protected — cek user */
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // WAJIB: level_id = 1 DAN role = 'owner'
        $isOwnerLevel = (int) $user->level_id === 1;
        $isOwnerRole  = method_exists($user, 'hasRole') && $user->hasRole('owner');

        if (! $isOwnerLevel || ! $isOwnerRole) {
            try {
                Log::channel('daily')->warning('[Security][GLOBAL] Blocked non-owner from owner path', [
                    'user_id'  => $user->id,
                    'level_id' => $user->level_id,
                    'path'     => $path,
                    'route'    => $routeName,
                    'ip'       => $request->ip(),
                ]);
            } catch (\Throwable $e) {}

            abort(403, 'Akses ditolak. Area ini hanya untuk Owner.');
        }

        return $next($request);
    }
}