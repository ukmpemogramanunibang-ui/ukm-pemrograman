<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Email belum diverifikasi.')
                : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}