<?php

namespace App\Http\Middleware;

use Closure;

class CheckOffline
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}