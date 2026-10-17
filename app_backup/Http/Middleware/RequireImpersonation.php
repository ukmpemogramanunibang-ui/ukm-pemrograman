<?php

namespace App\Http\Middleware;

use App\Services\Impersonation\ImpersonationManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class RequireImpersonation
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Owner asli HARUS login
        $realOwner = Auth::user();
        if (! $realOwner || (int) $realOwner->level_id !== 1) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login sebagai Owner.');
        }

        // 2. Cleanup expired
        ImpersonationManager::cleanupExpired();

        // 3. Ambil token dari route
        $token = $request->route('impersonationToken');
        if (! $token) return $this->safeExit('Token tidak ditemukan.');

        // 4. Cek di session
        $imp = ImpersonationManager::get($token);
        if (! $imp) return $this->safeExit('Sesi jelajah sudah kadaluarsa.');

        // 5. Ambil target user
        $targetUser = \App\Models\User::with('level')->find($imp['target_user_id'] ?? null);
        if (! $targetUser) {
            ImpersonationManager::forget($token);
            return $this->safeExit('User target tidak ditemukan.');
        }

        if ((int) $targetUser->level_id === 1) {
            ImpersonationManager::forget($token);
            return $this->safeExit('Tidak boleh menjelajahi Owner.');
        }

        // 6. Cek role_slug cocok dengan URL
        $roleSlug = $request->route('roleSlug');
        if ($roleSlug && ($imp['role_slug'] ?? null) !== $roleSlug) {
            return $this->safeExit('Role URL tidak cocok dengan sesi.');
        }

        // 7. Set current context
        session()->put('impersonation_current', [
            'token'          => $token,
            'target_user_id' => $targetUser->id,
            'role_slug'      => $imp['role_slug'],
            'owner_id'       => $imp['owner_id'],
        ]);

        // 8. Switch Auth ke target
        Auth::setUser($targetUser);

        View::share('isImpersonating',    true);
        View::share('impersonatedUser',   $targetUser);
        View::share('impersonationToken', $token);
        View::share('impersonationCount', ImpersonationManager::count());
        View::share('realOwner',          $realOwner);

        Log::debug('[Impersonation] switched', [
            'token'     => substr($token, 0, 8) . '...',
            'target_id' => $targetUser->id,
            'route'     => $request->route()?->getName(),
        ]);

        return $next($request);
    }

    protected function safeExit(string $message)
    {
        // Coba beberapa route, fallback URL literal
        foreach ([
            'owner.settings.explore.index',
            'owner.dashboard',
            'dashboard',
        ] as $name) {
            if (Route::has($name)) {
                return redirect()->route($name)->with('warning', $message);
            }
        }
        return redirect('/owner/settings/explore')->with('warning', $message);
    }
}