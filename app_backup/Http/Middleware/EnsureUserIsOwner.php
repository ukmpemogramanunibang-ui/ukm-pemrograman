<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOwner
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->level_id != 1) {
            abort(403, 'Akses ditolak. Hanya Owner yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}