<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
         $user = $request->user();
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Jika tidak memiliki role yang sesuai, redirect ke dashboard sesuai role
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
        return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
