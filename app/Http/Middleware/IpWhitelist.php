<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: sesuaikan daftar IP
        return $next($request);
    }
}