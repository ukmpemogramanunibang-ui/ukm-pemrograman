<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && method_exists($request->user(), 'isActive') && !$request->user()->isActive()) {
            abort(403, 'Akun tidak aktif.');
        }
        return $next($request);
    }
}