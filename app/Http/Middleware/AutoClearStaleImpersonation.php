<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoClearStaleImpersonation
{
    /**
     * Kalau ada jejak impersonation TAPI tidak ada owner asli yang login,
     * berarti session kotor dari aktivitas sebelumnya → bersihkan.
     */
    public function handle(Request $request, Closure $next)
    {
        $hasImpersonation = session()->has('impersonate_active')
            || session()->has('explore_user_id')
            || session()->has('impersonated_user_id')
            || session()->has('target_user_id');

        if ($hasImpersonation) {
            $impersonatorId = session('impersonator_id');
            $impersonator   = $impersonatorId ? \App\Models\User::find($impersonatorId) : null;

            // Owner asli tidak ada / bukan level 1 → purging
            if (! $impersonator || (int) $impersonator->level_id !== 1) {
                session()->forget([
                    'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
                    'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
                    'target_user_id', 'role_slug', 'impersonator_id',
                    'impersonate_role', 'explore_role',
                ]);
            }
        }

        return $next($request);
    }
}