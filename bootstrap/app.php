<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\BackedEnumCaseNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /* ---------- 1. TRUSTED PROXIES ---------- */
        $middleware->trustProxies(at: '*');

        /* ---------- 2. GLOBAL ---------- */
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        /* ---------- 3. WEB GROUP ---------- */
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CacheHeaders::class,
            \App\Http\Middleware\LogUserActivity::class,
            \App\Http\Middleware\ImpersonationGuard::class,
            \App\Http\Middleware\BlockNonOwnerGlobal::class,
             \App\Http\Middleware\SetOrganizationContext::class,
        ]);

        /* ---------- 4. API ---------- */
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        /* ---------- 5. ALIASES ---------- */
        $middleware->alias([
            // Role & Permission
            'role'           => \App\Http\Middleware\CheckRole::class,
            'permission'     => \App\Http\Middleware\CheckPermission::class,
            'owner.only'      => \App\Http\Middleware\EnsureOwnerOnly::class,
            'owner'          => \App\Http\Middleware\EnsureUserIsOwner::class,
            'owner.explorer' => \App\Http\Middleware\OwnerExplorer::class,
            'impersonate.isolate' => \App\Http\Middleware\IsolateImpersonationSession::class,
            'owner.explore.bypass' => \App\Http\Middleware\AllowOwnerExplore::class,
            // ✨ Jabatan — untuk route /pengurus/bendahara, /pdd, /pengembang
            'jabatan' => \App\Http\Middleware\CheckJabatan::class,

            // User Status
            'user-active'      => \App\Http\Middleware\UserActive::class,
            'profile-complete' => \App\Http\Middleware\ProfileComplete::class,
            'no-cache'         => \App\Http\Middleware\NoCache::class,
            'cache-control'    => \App\Http\Middleware\CacheHeaders::class,
            'force-json'       => \App\Http\Middleware\ForceJsonResponse::class,

            // Security
            'maintenance'  => \App\Http\Middleware\MaintenanceMode::class,
            'ip-whitelist' => \App\Http\Middleware\IpWhitelist::class,

            // LMS
            'lms.access'    => \App\Http\Middleware\EnsureLmsAccess::class,
            'lms.recipient' => \App\Http\Middleware\VerifiedRecipient::class,

            // Secretary
            'check.secretary' => \App\Http\Middleware\CheckSecretaryAccess::class,

            // Impersonation
            'impersonate.require' => \App\Http\Middleware\RequireImpersonation::class,
            'impersonate.guard'   => \App\Http\Middleware\ImpersonationGuard::class,
             'org.context' => \App\Http\Middleware\SetOrganizationContext::class,
        ]);

        /* ---------- 6. REDIRECT SAFETY ---------- */
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) return null;
            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            if ($user && method_exists($user, 'getDashboardRoute')) {
                try {
                    $route = $user->getDashboardRoute();
                    if ($route) {
                        if (str_starts_with($route, ['http://', 'https://', '/'])) {
                            return $route;
                        }
                        if (\Illuminate\Support\Facades\Route::has($route)) {
                            return route($route);
                        }
                    }
                } catch (\Throwable $e) {}
            }
            return \Illuminate\Support\Facades\Route::has('dashboard')
                ? route('dashboard')
                : '/dashboard';
        });

        /* ---------- 7. PRIORITY ---------- */
        $middleware->priority([
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
            \Livewire\Features\SupportDisablingBackButtonCache\DisableBackButtonCacheMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            if (! $request->is('login')) {
                session()->put('url.intended', $request->fullUrl());
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        });

        $exceptions->render(function (RouteNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Route tidak ditemukan.',
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
            return null;
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found.'], 404);
            }
            return null;
        });

        $exceptions->render(function (BackedEnumCaseNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid enum case.'], 400);
            }
            return null;
        });
    })
    ->create();