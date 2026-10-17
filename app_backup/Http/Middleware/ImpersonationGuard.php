<?php

namespace App\Http\Middleware;

use App\Services\Impersonation\ImpersonationManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        try { ImpersonationManager::cleanupExpired(); } catch (\Throwable $e) {}

        if (Auth::check()) {
            View::share('impersonationCount', ImpersonationManager::count());
            View::share('impersonationList',  ImpersonationManager::all());
            View::share('isImpersonating',    session()->has('impersonation_current'));
        }

        return $next($request);
    }
}