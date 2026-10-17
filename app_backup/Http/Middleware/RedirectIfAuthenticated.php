<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $redirectUrl = $this->redirectPath($user);
                return redirect($redirectUrl);
            }
        }

        return $next($request);
    }

    protected function redirectPath($user)
    {
        if ($user->hasRole('owner')) return route('owner.dashboard');
        if ($user->hasRole('superadmin')) return route('superadmin.dashboard');
        if ($user->hasRole('ketua')) return route('ketua.dashboard');
        if ($user->hasRole('pengurus')) return route('pengurus.dashboard');
        if ($user->hasRole('anggota')) return route('anggota.dashboard');
        return route('guest.dashboard');
    }
}