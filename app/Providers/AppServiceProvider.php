<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{
    View, Gate, RateLimiter, Blade, DB, URL, Cache, Log,
    Route, Validator, Notification as NotificationFacade, Auth,
    Paginator, Vite, Queue
};
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\Paginator as BasePaginator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Throwable;

// ── View Composers ──
use App\View\Composers\SidebarComposer;

// ── Models ──
use App\Models\Booking;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Financial\PaymentAllocation;
use App\Models\User;
use App\Models\Lms\LmsItem;

// ── Observers ──
use App\Observers\{
    PaymentAllocationObserver,
    AttendanceRecordObserver,
    AttendanceSessionObserver,
    BookingObserver,
    UserObserver,
    LmsItemObserver,
};

// ── Policies ──
use App\Policies\{
    AttendanceSessionPolicy,
    BookingPolicy,
    UserPolicy,
};

// ── Services ──
use App\Services\Notifications\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    private const SUPERADMIN_BYPASS = [
        'view-*', 'manage-users', 'manage-roles', 'manage-attendance',
        'manage-lms', 'approve-*', 'view-financial-reports', 'view-audit-log',
    ];

    public function register(): void
    {
        $this->registerConfigs();
        $this->registerSingletons();
        $this->registerRoutePatterns();
    }

    protected function registerConfigs(): void
    {
        foreach (['unicode_notifications', 'features', 'security'] as $config) {
            $path = config_path("{$config}.php");
            if (file_exists($path)) {
                $this->mergeConfigFrom($path, $config);
            }
        }
    }

    protected function registerSingletons(): void
    {
        $this->app->singleton(
            NotificationService::class,
            fn () => new NotificationService()
        );
        $this->app->alias(NotificationService::class, 'notifications');
    }

    protected function registerRoutePatterns(): void
    {
        Route::pattern('uuid', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');
        Route::pattern('nim',  '[0-9]{6,15}');
        Route::pattern('slug', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::pattern('token', '[A-Za-z0-9_\-]{16,}');
        Route::pattern('year', '[12][0-9]{3}');
    }

    public function boot(): void
    {
        $this->safe(fn () => $this->configureStrictMode());
        $this->safe(fn () => $this->configureMorphMap());
        $this->safe(fn () => $this->configureModelConfig());
        $this->safe(fn () => $this->configurePolicies());
        $this->safe(fn () => $this->configureGates());
        $this->safe(fn () => $this->configureObservers());
        $this->safe(fn () => $this->configureValidation());
        $this->safe(fn () => $this->configureRateLimiters());
        $this->safe(fn () => $this->configureBladeDirectives());
        $this->safe(fn () => $this->configureUrlForceHttps());
        $this->safe(fn () => $this->configureNotificationMacros());
        $this->safe(fn () => $this->configureViewComposers());
        $this->safe(fn () => $this->configureGlobalViewData());
        $this->safe(fn () => $this->configureSlowQueryLogger());
        $this->safe(fn () => $this->configureLogging());
        $this->safe(fn () => $this->configureHealthChecks());
        $this->safe(fn () => $this->configureMacros());
        $this->safe(fn () => $this->configureViteNonce());
        $this->safe(fn () => $this->configurePaginator());
        $this->safe(fn () => $this->configureDatabaseGuards());
        $this->safe(fn () => $this->configureQueueEvents());
        
        // ── Observers Khusus ──
        \App\Models\DashboardItem::observe(\App\Observers\DashboardItemObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }

    protected function safe(callable $fn): void
    {
        try {
            $fn();
        } catch (Throwable $e) {
            Log::error('[AppServiceProvider] Boot error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile() . ':' . $e->getLine(),
            ]);
        }
    }

    /* ──────────────────────────────────────────────────────────
     | CONFIGURATIONS
     * ────────────────────────────────────────────────────────── */

    protected function configureStrictMode(): void
    {
        if ($this->app->isProduction()) {
            if (config('app.prevent_lazy_loading', false)) {
                Model::preventLazyLoading();
            }
            Relation::requireMorphMap();
            return;
        }

        if (! $this->app->runningUnitTests()) {
            Model::shouldBeStrict(true);
            Model::preventLazyLoading(true);
            Model::preventSilentlyDiscardingAttributes(true);
        }

        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            Log::warning('[LazyLoading] N+1 terdeteksi', [
                'model'    => get_class($model),
                'relation' => $relation,
                'url'      => request()?->fullUrl(),
                'user_id'  => Auth::id(),
            ]);
        });
    }

    protected function configureMorphMap(): void
    {
        Relation::enforceMorphMap([
            'booking'            => Booking::class,
            'user'               => User::class,
            'payment_allocation' => PaymentAllocation::class,
            'attendance_session' => AttendanceSession::class,
            'attendance_record'  => AttendanceRecord::class,
            'lms_item'           => LmsItem::class,
        ]);
    }

    protected function configureModelConfig(): void
    {
        Model::unguard(false);
    }

    protected function configurePolicies(): void
    {
        Gate::policy(AttendanceSession::class, AttendanceSessionPolicy::class);
        Gate::policy(Booking::class,           BookingPolicy::class);
        Gate::policy(User::class,              UserPolicy::class);

        if (class_exists(\App\Policies\LmsItemPolicy::class)) {
            Gate::policy(LmsItem::class, \App\Policies\LmsItemPolicy::class);
        }
    }

    protected function configureGates(): void
    {
        Gate::before(function (User $user, string $ability) {
            if (! $user->hasRole('superadmin')) return null;
            foreach (self::SUPERADMIN_BYPASS as $pattern) {
                if (Str::is($pattern, $ability)) return true;
            }
            return null;
        });

        Gate::define('access-owner-area',  fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']) && $u->is_active);
        Gate::define('access-admin-area',  fn (User $u) => $u->hasAnyRole(['admin', 'owner', 'superadmin']) && $u->is_active);
        Gate::define('access-member-area', fn (User $u) => (bool) $u->is_active);
        Gate::define('manage-system-settings', fn (User $u) => $u->hasRole('superadmin'));
        Gate::define('manage-users',           fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('manage-roles',           fn (User $u) => $u->hasRole('superadmin'));
        Gate::define('promote-demote',         fn (User $u) => $u->hasRole('owner'));
        Gate::define('view-financial-reports', fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'finance']));
        Gate::define('approve-payments',       fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'finance']));
        Gate::define('export-sensitive-data',  fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('view-audit-log',         fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('force-delete',           fn (User $u) => $u->hasRole('owner'));
        Gate::define('approve-booking',        fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'admin']));
        Gate::define('manage-attendance',      fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'admin']));
        Gate::define('manage-lms',             fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'admin']));
    }

    protected function configureObservers(): void
    {
        $map = [
            PaymentAllocation::class => PaymentAllocationObserver::class,
            AttendanceRecord::class  => AttendanceRecordObserver::class,
            AttendanceSession::class => AttendanceSessionObserver::class,
            Booking::class           => BookingObserver::class,
            User::class              => UserObserver::class,
            LmsItem::class           => LmsItemObserver::class,
        ];

        foreach ($map as $model => $observer) {
            if (! class_exists($observer) || ! class_exists($model)) continue;
            try {
                $model::observe($observer);
            } catch (Throwable $e) {
                Log::error("[AppServiceProvider] Gagal register observer", [
                    'model' => $model, 'observer' => $observer, 'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function configureValidation(): void
    {
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers();
            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        Validator::extend('phone_id', fn ($a, $v) => preg_match('/^(\+62|62|0)8[1-9][0-9]{6,11}$/', (string) $v) === 1, 'Format nomor HP tidak valid');
        Validator::extend('nim', fn ($a, $v) => preg_match('/^[0-9]{8,15}$/', (string) $v) === 1, 'Format NIM tidak valid');
        Validator::extend('slug', fn ($a, $v) => preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $v) === 1, 'Format slug tidak valid');
        Validator::extend('clean_text', function ($a, $v) {
            $banned = ['<script', 'javascript:', 'onerror=', 'onload=', 'data:text/html'];
            $lower  = Str::lower((string) $v);
            foreach ($banned as $b) {
                if (Str::contains($lower, $b)) return false;
            }
            return true;
        }, 'Teks mengandung karakter yang tidak diizinkan');
    }

    protected function configureRateLimiters(): void
    {
        // API
        RateLimiter::for('api',              fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-login',        fn ($r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('web', fn (Request $r) => Limit::perMinute(120)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('login', function (Request $r) {
            $key = Str::lower((string) $r->input('email')) . '|' . $r->ip();
            return [
                Limit::perMinute(5)->by('login:' . $key)->response(fn () => back()->withErrors(['email' => 'Terlalu banyak percobaan login.'])),
                Limit::perDay(50)->by('login-day:' . $r->ip()),
            ];
        });
        RateLimiter::for('account-check', function (Request $r) {
            $identifier = hash_hmac('sha256', (string) $r->input('value'), config('app.key'));
            return [
                Limit::perMinute(30)->by('check-ip:' . $r->ip()),
                Limit::perMinute(5)->by('check-id:' . $identifier),
            ];
        });
        RateLimiter::for('notifications-poll',   fn (Request $r) => Limit::perMinute(20)->by('notif-poll:' . $r->user()?->id));
        RateLimiter::for('search', fn (Request $r) => [
            Limit::perMinute(30)->by('search:' . ($r->user()?->id ?: $r->ip())),
            Limit::perMinute(5)->by('search-burst:' . $r->ip()),
        ]);
    }

    protected function configureBladeDirectives(): void
    {
        Blade::if('role',        fn (string $role)  => Auth::check() && Auth::user()->hasRole($role));
        Blade::if('anyrole',     fn (array $roles)  => Auth::check() && Auth::user()->hasAnyRole($roles));
        Blade::if('haslevel', function (int|array $levels) {
            if (! Auth::check()) return false;
            $current = (int) (Auth::user()->level_id ?? 0);
            return is_array($levels) ? in_array($current, $levels, true) : $current === (int) $levels;
        });
        Blade::if('active', function (string ...$patterns) {
            foreach ($patterns as $p) {
                if (request()->routeIs($p)) return true;
            }
            return false;
        });
        Blade::directive('money',    fn ($expr) => "<?php echo 'Rp ' . number_format({$expr}, 0, ',', '.'); ?>");
        Blade::directive('datetime', fn ($expr) => "<?php echo ({$expr})?->translatedFormat('d M Y, H:i'); ?>");
        Blade::directive('timeago',  fn ($expr) => "<?php echo ({$expr})?->diffForHumans(); ?>");
    }

    protected function configureUrlForceHttps(): void
    {
        if ($this->app->isProduction() && config('app.force_https', true)) {
            URL::forceScheme('https');
        }
        if (config('app.trust_proxies', false)) {
            Request::setTrustedProxies(['*'], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
        }
        URL::defaults(['locale' => app()->getLocale()]);
    }

    protected function configureNotificationMacros(): void
    {
        NotificationFacade::macro('sendToRole', function (string|array $roles, $notification) {
            $users = User::role($roles)->where('is_active', true)->get();
            foreach ($users as $user) $user->notify(clone $notification);
            return $users->count();
        });
    }

    protected function configureViewComposers(): void
    {
        View::composer('partials.roles.owner.header', function ($view) {
            $view->with('pendingBookings', Cache::remember('header.pending_bookings', now()->addMinutes(2), fn () => Booking::where('status', 'Menunggu Persetujuan')->count()));
        });
        View::composer(['partials.roles.*.header', 'partials.header', 'layouts.app', 'layouts.owner', 'layouts.roles.*'], function ($view) {
            $user = function_exists('effective_user') ? effective_user() : Auth::user();
            if (! $user) return;
            $view->with([
                'authUser'     => $user,
                'unreadNotifs' => Cache::remember("user.{$user->id}.unread_notifs", now()->addSeconds(30), fn () => $user->unreadNotifications()->count()),
                'userRole'  => $user->roles->pluck('name')->first() ?? 'member',
                'userLevel' => $user->level_id ?? 0,
            ]);
        });
        View::composer(['partials.sidebar', 'partials.roles.*.sidebar'], SidebarComposer::class);
    }

    protected function configureGlobalViewData(): void
    {
        View::composer('*', function ($view) {
            $view->with([
                'pending_payments' => $this->safeCount(fn () => (int) Cache::remember('global.pending_payments', 60, function () {
                    $inv = \App\Models\Financial\Invoice::class;
                    if (class_exists($inv)) { try { return $inv::where('status', 'pending')->count(); } catch (Throwable $e) { return 0; } }
                    return 0;
                })),
                'isImpersonating' => request()->routeIs('owner.impersonate.*'),
            ]);
        });
    }

    protected function safeCount(callable $fn): int
    {
        try { return (int) $fn(); } catch (Throwable $e) { return 0; }
    }

    protected function configureSlowQueryLogger(): void
    {
        if (! $this->app->isLocal() && ! config('app.debug')) return;
        if (! config('app.log_slow_query', false)) return;
        $thresholdMs = (int) config('app.slow_query_ms', 500);
        DB::listen(function ($query) use ($thresholdMs) {
            if ($query->time < $thresholdMs) return;
            try {
                Log::channel('performance')->warning('Slow Query Detected', [
                    'sql' => $query->sql, 'time_ms' => $query->time, 'url' => request()->fullUrl(),
                ]);
            } catch (Throwable $e) {}
        });
    }

    protected function configureLogging(): void
    {
        try {
            if ($this->app->runningInConsole()) return;
            $requestId = request()->header('X-Request-ID') ?? (string) Str::uuid();
            app()->instance('request_id', $requestId);
            Log::withContext(['request_id' => $requestId, 'user_id' => Auth::id(), 'url' => request()->fullUrl()]);
        } catch (Throwable $e) {}
    }

    protected function configureHealthChecks(): void
    {
        if (! class_exists(\Spatie\Health\Facades\Health::class)) return;
        try {
            \Spatie\Health\Facades\Health::checks([
                \Spatie\Health\Checks\Checks\DatabaseCheck::new(),
                \Spatie\Health\Checks\Checks\CacheCheck::new(),
                \Spatie\Health\Checks\Checks\DebugModeCheck::new(),
                \Spatie\Health\Checks\Checks\EnvironmentCheck::new(),
                \Spatie\Health\Checks\Checks\OptimizedAppCheck::new(),
                \Spatie\Health\Checks\Checks\ScheduleCheck::new(),
            ]);
        } catch (Throwable $e) {}
    }

    protected function configureMacros(): void
    {
        Blueprint::macro('uuidPrimary', function () { $this->uuid('id')->primary(); });
        Blueprint::macro('userstamps', function () {
            $this->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
        Str::macro('initials', function (string $name, int $max = 2): string {
            return collect(preg_split('/\s+/', trim($name)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take($max)->implode('');
        });
    }

    protected function configureViteNonce(): void
    {
        try {
            if (! app()->bound('csp_nonce')) {
                app()->instance('csp_nonce', Str::random(32));
            }
        } catch (Throwable $e) {}
    }

    protected function configurePaginator(): void
    {
        BasePaginator::useBootstrapFive();
    }

    protected function configureDatabaseGuards(): void
    {
        if ($this->app->isProduction()) {
            DB::prohibitDestructiveCommands(true);
        }
    }

    protected function configureQueueEvents(): void
    {
        if ($this->app->runningInConsole()) return;
        Queue::failing(function ($event) {
            Log::error('[Queue Failing]', [
                'connection' => $event->connectionName, 'job' => $event->job?->resolveName(), 'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}