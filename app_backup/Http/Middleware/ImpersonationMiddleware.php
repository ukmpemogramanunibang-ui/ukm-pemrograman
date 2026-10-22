<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ImpersonationMiddleware
{
    /**
     * Session key yang menandakan owner sedang menjelajah.
     */
    protected array $impersonateKeys = [
        'impersonate_target_id',
        'impersonated_user_id',
        'impersonate_user_id',
        'explore_user_id',
        'target_user_id',
    ];

    protected array $cleanupKeys = [
        'impersonate_active',
        'impersonate_target_id',
        'impersonate_role_slug',
        'impersonated_user_id',
        'impersonate_user_id',
        'explore_user_id',
        'target_user_id',
        'role_slug',
        'impersonator_id',
    ];

    public function handle(Request $request, Closure $next)
    {
        $targetId = $this->resolveTargetId();

        // ── 1. Tidak ada sesi jelajah → redirect dengan aman ──
        if (! $targetId) {
            return $this->safeRedirect('Sesi jelajah tidak ditemukan. Silakan mulai ulang.');
        }

        // ── 2. Target user tidak ada → bersihkan, redirect ──
        $target = User::find($targetId);
        if (! $target) {
            session()->forget($this->cleanupKeys);
            return $this->safeRedirect('User target tidak ditemukan. Sesi jelajah direset.');
        }

        // ── 3. Set canonical flag (dipakai sidebar/header/guard) ──
        session([
            'impersonate_active'    => true,
            'impersonate_target_id' => $target->id,
            'impersonate_role_slug' => $this->resolveRoleSlug($request),
        ]);

        // ── 4. Simpan owner asli sekali saja ──
        if (! session()->has('impersonator_id')) {
            session(['impersonator_id' => Auth::id()]);
        }

        // ── 5. Swap Auth user supaya view baca data target ──
        Auth::setUser($target);

        View::share('isImpersonating', true);
        View::share('impersonatedUser', $target);

        Log::debug('[Impersonation] swapped', [
            'owner_id'  => session('impersonator_id'),
            'target_id' => $target->id,
            'route'     => $request->route()?->getName(),
            'url'       => $request->fullUrl(),
        ]);

        return $next($request);
    }

    /* ────────────────────────────────────────────────────────
     | HELPERS
     * ──────────────────────────────────────────────────────── */

    protected function resolveTargetId(): ?int
    {
        foreach ($this->impersonateKeys as $key) {
            if (session()->has($key)) {
                $val = session($key);
                if ($val) return (int) $val;
            }
        }
        return null;
    }

    protected function resolveRoleSlug(Request $request): string
    {
        return session('impersonate_role_slug')
            ?? session('impersonate_role')
            ?? session('explore_role')
            ?? session('role_slug')
            ?? $request->route('roleSlug')
            ?? 'anggota';
    }

    /**
     * Redirect yang TIDAK akan pernah error RouteNotFoundException.
     * Coba beberapa kandidat route, fallback ke URL literal.
     */
protected function safeRedirect(string $message)
{
    // Cek dulu apakah route 'owner.settings.explore.index' ada
    if (\Illuminate\Support\Facades\Route::has('owner.settings.explore.index')) {
        return redirect()->route('owner.settings.explore.index')->with('error', $message);
    }

    // Fallback: arahkan ke owner dashboard
    if (\Illuminate\Support\Facades\Route::has('owner.dashboard')) {
        return redirect()->route('owner.dashboard')->with('error', $message);
    }

    // Fallback terakhir: bersihkan sesi & lempar ke login
    session()->forget([
        'impersonate_active', 'impersonate_target_id', 'impersonate_role_slug',
        'impersonated_user_id', 'impersonate_user_id', 'explore_user_id',
        'target_user_id', 'role_slug', 'impersonator_id',
    ]);

    return redirect('/login')->with('error', $message);
}
}