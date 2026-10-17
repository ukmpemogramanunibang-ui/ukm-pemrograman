<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * --------------------------------------------------------------------------
     * HOME PATH
     * --------------------------------------------------------------------------
     * Path default setelah user berhasil login.
     *
     * ⚠️ JANGAN arahkan ke halaman yang butuh impersonation
     *    (mis. /owner/settings/explore) karena akan memicu redirect loop.
     *
     * Gunakan '/dashboard' — nanti controller dashboard yang akan
     * mengarahkan user ke halaman sesuai role-nya via getDashboardRoute().
     */
    public const HOME = '/dashboard';

    /**
     * Path fallback jika route 'dashboard' tidak tersedia.
     */
    public const HOME_FALLBACK = '/login';

    /**
     * --------------------------------------------------------------------------
     * BOOT
     * --------------------------------------------------------------------------
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureRouteBindings();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * --------------------------------------------------------------------------
     * REDIRECT HELPER — Aman dari RouteNotFoundException
     * --------------------------------------------------------------------------
     * Fungsi ini bisa dipakai sebagai pengganti `RouteServiceProvider::HOME`
     * di controller/middleware agar tidak pernah error.
     */
    public static function redirectToHome(Request $request = null): string
    {
        $request = $request ?? request();

        // 1. Jika user login & punya method getDashboardRoute() → pakai itu
        if ($user = $request->user()) {
            if (method_exists($user, 'getDashboardRoute')) {
                try {
                    $route = $user->getDashboardRoute();
                    if ($route) {
                        // Kalau return-nya berupa URL langsung
                        if (Str::startsWith($route, ['http://', 'https://', '/'])) {
                            return $route;
                        }
                        // Kalau return-nya nama route
                        if (Route::has($route)) {
                            return route($route);
                        }
                    }
                } catch (\Throwable $e) {
                    // diamkan — fallback ke bawah
                }
            }
        }

        // 2. Fallback ke nama route 'dashboard' jika ada
        if (Route::has('dashboard')) {
            return route('dashboard');
        }

        // 3. Fallback terakhir: URL literal
        return url(self::HOME);
    }

    /**
     * --------------------------------------------------------------------------
     * RATE LIMITERS
     * --------------------------------------------------------------------------
     */
    protected function configureRateLimiting(): void
    {
        // ── API umum ──
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // ── Login manual ──
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // ── Kontak publik ──
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // ── Cek akun (cek NIM, QR, NFC) ──
        RateLimiter::for('account-check', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // ── Reset password ──
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // ── Polling notifikasi ──
        RateLimiter::for('notifications-poll', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // ── Global fallback ──
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * --------------------------------------------------------------------------
     * ROUTE MODEL BINDINGS
     * --------------------------------------------------------------------------
     * Daftarkan custom binding di sini agar konsisten.
     */
    protected function configureRouteBindings(): void
    {
        // Bind berdasarkan UUID
        Route::bind('uuid', function ($value) {
            return \App\Models\User::where('user_uuid', $value)->firstOrFail();
        });

        // Optional: binding User default pakai 'id' (biarkan default Laravel)
        // Kalau mau pakai 'user_uuid' sebagai route key, uncomment:
        // Route::bind('user', function ($value) {
        //     return \App\Models\User::where('user_uuid', $value)->firstOrFail();
        // });
    }
}