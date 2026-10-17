<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class Kernel extends HttpKernel
{
    /* ============================================================
     | GLOBAL MIDDLEWARE
     | Berjalan di setiap request (web & api)
     * ============================================================ */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
\App\Http\Middleware\AutoClearStaleImpersonation::class,   // ← TAMBAHKAN
    \App\Http\Middleware\ImpersonateGuard::class,
        // 🔒 Keamanan & performa global
        \App\Http\Middleware\SecurityHeaders::class,       // X-Frame-Options, CSP, dll
        \App\Http\Middleware\SetLocale::class,             // Set bahasa dari session/user
    ];

    /* ============================================================
     | MIDDLEWARE GROUPS
     * ============================================================ */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Custom UNICODE
            \App\Http\Middleware\CacheHeaders::class,
            \App\Http\Middleware\LogUserActivity::class,   // Audit log (opsional)
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ForceJsonResponse::class,
        ],

        // Grup khusus Owner (bisa dipakai di route group)
        'owner' => [
            'web',
            'auth',
            'user-active',
            'role:owner,superadmin',
            'cache-control',
        ],
    ];

    /* ============================================================
     | MIDDLEWARE ALIASES
     | Laravel 10/11: bisa via $middlewareAliases (deprecated) atau
     | via bootstrap/app.php (Laravel 11). Tetap dipakai untuk kompat.
     * ============================================================ */
    protected $middlewareAliases = [
        /* ---------- Laravel Default ---------- */
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'              => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive'     => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed'           => \App\Http\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'impersonate.isolate' => \App\Http\Middleware\IsolateImpersonationSession::class,

        /* ---------- Custom UNICODE ---------- */
        // Akses & Role
        'role'             => \App\Http\Middleware\CheckRole::class,
        'permission'       => \App\Http\Middleware\CheckPermission::class,
        'owner'            => \App\Http\Middleware\EnsureUserIsOwner::class,
        'owner.explorer'   => \App\Http\Middleware\OwnerExplorer::class,
        'check.secretary'  => \App\Http\Middleware\CheckSecretaryAccess::class,

        // State User
        'user-active'      => \App\Http\Middleware\UserActive::class,
        'profile-complete' => \App\Http\Middleware\ProfileComplete::class,

        // Cache & Response
        'no-cache'         => \App\Http\Middleware\NoCache::class,
        'cache-control'    => \App\Http\Middleware\CacheHeaders::class,
        'force-json'       => \App\Http\Middleware\ForceJsonResponse::class,

        // Maintenance & Security
        'maintenance'      => \App\Http\Middleware\MaintenanceMode::class,
        'ip-whitelist'     => \App\Http\Middleware\IpWhitelist::class,
        'security-headers' => \App\Http\Middleware\SecurityHeaders::class,

        // LMS
        'lms.access'       => \App\Http\Middleware\EnsureLmsAccess::class,
        'lms.recipient'    => \App\Http\Middleware\VerifiedRecipient::class,

        // Rate limit helper (untuk dipakai inline di route)
        'throttle.login'   => 'throttle:login',
        'throttle.api'     => 'throttle:api',
        'throttle.notif'   => 'throttle:notifications',
        'throttle.booking' => 'throttle:booking',
    ];

    /**
     * Backward-compat: beberapa package masih cek $routeMiddleware.
     * Biarkan sinkron dengan $middlewareAliases.
     */
    protected $routeMiddleware = [
        'owner'            => \App\Http\Middleware\EnsureUserIsOwner::class,
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'role'             => \App\Http\Middleware\CheckRole::class,
        'permission'       => \App\Http\Middleware\CheckPermission::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'no-cache'         => \App\Http\Middleware\NoCache::class,
        'cache-control'    => \App\Http\Middleware\CacheHeaders::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'check.secretary'  => \App\Http\Middleware\CheckSecretaryAccess::class,
    ];

    /* ============================================================
     | MIDDLEWARE PRIORITY
     | Urutan eksekusi middleware non-global
     * ============================================================ */
    protected $middlewarePriority = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

    /* ============================================================
     | RATE LIMITERS
     | Definisikan limit per nama — dipanggil via 'throttle:{name}'
     * ============================================================ */
    protected function configureRateLimiting(): void
    {
        // 🌐 API umum
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak permintaan. Coba lagi nanti.',
                    ], 429, $headers);
                });
        });

        // 🔐 Login — cegah brute force
        RateLimiter::for('login', function (Request $request) {
            $key = strtolower($request->input('email')) . '|' . $request->ip();
            return [
                Limit::perMinute(5)->by($key),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        // 🔔 Poll notifikasi header — 1 user poll tiap 10s, batasi
        RateLimiter::for('notifications', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // 📅 Booking — cegah spam submit
        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // 📤 Upload file
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        // 🛡️ Password reset & email verifikasi
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });
    }
}