<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // ═══════════════════════════════════════════════════════════
        // 1. IMPERSONATION AWARE
        //    Jika Owner sedang menjelajah akun, dan role yang diminta
        //    cocok dengan role_slug target → izinkan lewat.
        // ═══════════════════════════════════════════════════════════
        $imp = session('impersonation');
        if (is_array($imp) && ($imp['active'] ?? false)) {
            $targetSlug = $imp['role_slug'] ?? null;

            foreach ($roles as $role) {
                if ($role === $targetSlug) {
                    return $next($request);
                }
            }
        }

        // ═══════════════════════════════════════════════════════════
        // 2. ROLE CHECK NORMAL
        // ═══════════════════════════════════════════════════════════
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // ═══════════════════════════════════════════════════════════
        // 3. REDIRECT — cerdas: kalau impersonation aktif,
        //    jangan lempar ke owner.dashboard, tapi ke halaman
        //    yang sesuai target.
        // ═══════════════════════════════════════════════════════════
        if (is_array($imp) && ($imp['active'] ?? false) && ! empty($imp['role_slug'])) {
            $slug = $imp['role_slug'];
            if (\Illuminate\Support\Facades\Route::has("owner.impersonate.dashboard")) {
                return redirect()
                    ->route('owner.impersonate.dashboard', ['roleSlug' => $slug])
                    ->with('warning', 'Halaman ini tidak tersedia untuk akun yang sedang dijelajahi.');
            }
        }

        if ($user->hasRole('owner')) {
            return redirect()->route('owner.dashboard');
        } elseif ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->hasRole('ketua')) {
            return redirect()->route('ketua.dashboard');
        } elseif ($user->hasRole('pengurus')) {
            return redirect()->route('pengurus.dashboard');
        } elseif ($user->hasRole('anggota')) {
            return redirect()->route('anggota.dashboard');
        }

        return redirect()->route('guest.dashboard');
    }
}