<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !$request->routeIs('offline*') && !$request->routeIs('offline.check')) {
            // Log aktivitas user (opsional)
            // AuditLog::create([...]);
        }

        return $next($request);
    }
}