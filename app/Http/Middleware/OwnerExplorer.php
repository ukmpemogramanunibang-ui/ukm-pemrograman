<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OwnerExplorer
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            auth()->check() && auth()->user()->level_id == 1,
            403,
            'Akses Explorer hanya untuk Owner.'
        );

        abort_unless(
            session('owner_explorer.active') === true,
            403,
            'Mode Explorer belum diverifikasi.'
        );

        return $next($request);
    }
}