<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
   
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            // status adalah relasi ke tabel statuses
            if ($user->status && in_array($user->status->name, ['suspended', 'blocked', 'expired', 'deleted'])) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Hubungi admin.');
            }
        }

        return $next($request);
    }
}
