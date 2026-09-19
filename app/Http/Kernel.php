<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];
protected function schedule(Schedule $schedule)
{
    // Setiap menit: update booking yang sudah lewat menjadi completed
    $schedule->call(function () {
        Booking::where('status', 'approved')
            ->where('end_datetime', '<', now())
            ->update(['status' => 'completed']);
    })->everyMinute();

    // Setiap 5 menit: cek ghost booking (tidak check-in dalam 30 menit setelah start)
    $schedule->call(function () {
        $gracePeriod = 30; // menit
        Booking::where('status', 'approved')
            ->where('start_datetime', '<', now()->subMinutes($gracePeriod))
            ->whereNull('checkin_at')
            ->update(['status' => 'expired']);
    })->everyFiveMinutes();

    // Update status ruangan (opsional, karena status bisa dihitung dari booking)
    // Anda bisa membuat job tersendiri
}
    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\CacheHeaders::class, // Custom: Cache control
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ForceJsonResponse::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // Laravel Default
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'owner.explorer' => \App\Http\Middleware\OwnerExplorer::class,
        
        // ============================================================
        // CUSTOM MIDDLEWARE UNICODE
        // ============================================================
        'role' => \App\Http\Middleware\CheckRole::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'no-cache' => \App\Http\Middleware\NoCache::class,
        'cache-control' => \App\Http\Middleware\CacheHeaders::class,
        'force-json' => \App\Http\Middleware\ForceJsonResponse::class,
        'maintenance' => \App\Http\Middleware\MaintenanceMode::class,
        'ip-whitelist' => \App\Http\Middleware\IpWhitelist::class,
        'user-active' => \App\Http\Middleware\UserActive::class,
        'profile-complete' => \App\Http\Middleware\ProfileComplete::class,
        'throttle.login' => \Illuminate\Routing\Middleware\ThrottleRequests::class . ':5,60',
    ];

    /**
     * Register the application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // Custom middleware yang bisa digunakan di route secara individual
        'owner' => \App\Http\Middleware\EnsureUserIsOwner::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'no-cache' => \App\Http\Middleware\NoCache::class,
        'cache-control' => \App\Http\Middleware\CacheHeaders::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'check.secretary' => \App\Http\Middleware\CheckSecretaryAccess::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
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
}