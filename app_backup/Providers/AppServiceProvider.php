<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{
    View, Gate, RateLimiter, Blade, DB, URL, Cache, Log,
    Route, Validator, Notification as NotificationFacade, Auth
};
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Throwable;

// ── View Composers ──────────────────────────────────────────────────
use App\View\Composers\SidebarComposer;

// ── Models ──────────────────────────────────────────────────────────
use App\Models\Booking;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Financial\PaymentAllocation;
use App\Models\User;
use App\Models\Lms\LmsItem;

// ── Observers ───────────────────────────────────────────────────────
use App\Observers\{
    PaymentAllocationObserver,
    AttendanceRecordObserver,
    AttendanceSessionObserver,
    BookingObserver,
    UserObserver,
    LmsItemObserver,
};

// ── Policies ────────────────────────────────────────────────────────
use App\Policies\{
    AttendanceSessionPolicy,
    BookingPolicy,
    UserPolicy,
};

// ── Services ────────────────────────────────────────────────────────
use App\Services\Notifications\NotificationService;

class AppServiceProvider extends ServiceProvider
{
    /* ==============================================================
     | REGISTER — Binding ke container (sebelum boot)
     * ============================================================== */
    public function register(): void
    {
        $this->registerConfigs();
        $this->registerSingletons();
        $this->registerHelpers();
    }

    protected function registerConfigs(): void
    {
        foreach (['unicode_notifications', 'features'] as $config) {
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

    protected function registerHelpers(): void
    {
       if (! function_exists('effective_user')) {
    function effective_user(): ?User {
        // 1. Cek route impersonate dulu
        // 2. Cek session flag canonical
        // 3. Cek session key individual
        $targetId = null;

        if (session('impersonate_active')) {
            $targetId = session('impersonate_target_id');
        }

        if (! $targetId) {
            $targetId = session('impersonated_user_id')
                ?? session('impersonate_user_id')
                ?? session('explore_user_id')
                ?? session('target_user_id');
        }

        if ($targetId && $u = User::find($targetId)) {
            return $u;
        }

        return Auth::user();
    }
}

if (! function_exists('is_impersonating')) {
    function is_impersonating(): bool {
        if (session('impersonate_active')) return true;
        if (request()->routeIs('owner.impersonate.*')) return true;

        foreach ([
            'impersonating','impersonate','impersonation',
            'impersonated_user_id','impersonate_user_id',
            'explore_user_id','target_user_id','impersonator_id',
        ] as $key) {
            if (session()->has($key)) return true;
        }
        return false;
    }
}
    }

    /* ==============================================================
     | BOOT
     * ============================================================== */
    public function boot(): void
    {
        $this->configureStrictMode();
        $this->configureMorphMap();
        $this->configureModelConfig();        // ← method ini ada di bawah
        $this->configurePolicies();
        $this->configureGates();
        $this->configureObservers();
        $this->configureValidation();
        $this->configureRateLimiters();
        $this->configureBladeDirectives();
        $this->configureUrlForceHttps();
        $this->configureNotificationMacros();
        $this->configureViewComposers();
        $this->configureGlobalViewData();
        $this->configureSlowQueryLogger();
        $this->configureLogging();
        $this->configureHealthChecks();
    }

    /* ==============================================================
     | 1. STRICT MODE
     * ============================================================== */
    protected function configureStrictMode(): void
    {
        if ($this->app->isProduction()) {
            if (config('app.prevent_lazy_loading', false)) {
                Model::preventLazyLoading();
            }
            return;
        }

        Model::shouldBeStrict(! $this->app->runningUnitTests());
        Model::preventLazyLoading(true);
        Model::preventSilentlyDiscardingAttributes(true);
        Model::preventAccessingMissingAttributes(true);
    }

    /* ==============================================================
     | 2. MORPH MAP
     * ============================================================== */
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

    /* ==============================================================
     | 3. MODEL CONFIG — guard aktif untuk keamanan mass assignment
     * ============================================================== */
    protected function configureModelConfig(): void
    {
        Model::unguard(false);
    }

    /* ==============================================================
     | 4. POLICIES
     * ============================================================== */
    protected function configurePolicies(): void
    {
        Gate::policy(AttendanceSession::class, AttendanceSessionPolicy::class);
        Gate::policy(Booking::class,           BookingPolicy::class);
        Gate::policy(User::class,              UserPolicy::class);

        if (class_exists(\App\Policies\LmsItemPolicy::class)) {
            Gate::policy(LmsItem::class, \App\Policies\LmsItemPolicy::class);
        }
    }

    /* ==============================================================
     | 5. GATES
     * ============================================================== */
    protected function configureGates(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });

        Gate::define('access-owner-area', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin']) && $u->is_active
        );
        Gate::define('access-admin-area', fn (User $u) =>
            $u->hasAnyRole(['admin', 'owner', 'superadmin']) && $u->is_active
        );
        Gate::define('access-member-area', fn (User $u) =>
            (bool) $u->is_active
        );

        Gate::define('manage-system-settings', fn (User $u) => $u->hasRole('superadmin'));
        Gate::define('manage-users',           fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('manage-roles',           fn (User $u) => $u->hasRole('superadmin'));

        Gate::define('view-financial-reports', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin', 'finance'])
        );
        Gate::define('approve-payments', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin', 'finance'])
        );

        Gate::define('export-sensitive-data', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin'])
        );
        Gate::define('view-audit-log', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin'])
        );
        Gate::define('force-delete', fn (User $u) => $u->hasRole('superadmin'));

        Gate::define('approve-booking', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin', 'admin'])
        );

        Gate::define('manage-attendance', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin', 'admin'])
        );

        Gate::define('manage-lms', fn (User $u) =>
            $u->hasAnyRole(['owner', 'superadmin', 'admin'])
        );
    }

    /* ==============================================================
     | 6. OBSERVERS
     * ============================================================== */
    protected function configureObservers(): void
    {
        $map = [
            PaymentAllocation::class  => PaymentAllocationObserver::class,
            AttendanceRecord::class   => AttendanceRecordObserver::class,
            AttendanceSession::class  => AttendanceSessionObserver::class,
            Booking::class            => BookingObserver::class,
            User::class               => UserObserver::class,
            LmsItem::class            => LmsItemObserver::class,
        ];

        foreach ($map as $model => $observer) {
            if (! class_exists($observer)) {
                Log::warning("[AppServiceProvider] Observer tidak ditemukan: {$observer} (untuk {$model}) — di-skip.");
                continue;
            }
            if (! class_exists($model)) {
                Log::warning("[AppServiceProvider] Model tidak ditemukan: {$model} (observer {$observer} tidak diregister).");
                continue;
            }
            try {
                $model::observe($observer);
            } catch (Throwable $e) {
                Log::error("[AppServiceProvider] Gagal register observer {$observer} untuk model {$model}", [
                    'model'    => $model,
                    'observer' => $observer,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }

    /* ==============================================================
     | 7. VALIDATION
     * ============================================================== */
    protected function configureValidation(): void
    {
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers();
            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        Validator::extend('phone_id', function ($attribute, $value) {
            return preg_match('/^(\+62|62|0)8[1-9][0-9]{6,11}$/', (string) $value) === 1;
        }, 'Format nomor HP tidak valid (contoh: 08123456789)');

        Validator::extend('nim', function ($attribute, $value) {
            return preg_match('/^[0-9]{8,15}$/', (string) $value) === 1;
        }, 'Format NIM tidak valid');

        Validator::extend('slug', function ($attribute, $value) {
            return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value) === 1;
        }, 'Format slug tidak valid');

        Validator::extend('clean_text', function ($attribute, $value) {
            $banned = ['<script', 'javascript:', 'onerror=', 'onload='];
            $lower  = Str::lower((string) $value);
            foreach ($banned as $b) {
                if (Str::contains($lower, $b)) return false;
            }
            return true;
        }, 'Teks mengandung karakter yang tidak diizinkan');
    }

    /* ==============================================================
     | 8. RATE LIMITERS
     * ============================================================== */
    protected function configureRateLimiters(): void
    {
        RateLimiter::for('api',            fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-ping',       fn ($r)        => Limit::perMinute(120)->by($r->ip()));
        RateLimiter::for('api-login',      fn ($r)        => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('api-auth',       fn ($r)        => Limit::perMinute(300)->by(optional($r->user())->id ?: $r->ip()));
        RateLimiter::for('api-legacy',     fn ($r)        => Limit::perMinute(60)->by(optional($r->user())->id ?: $r->ip()));
        RateLimiter::for('api-nfc-ping',   fn ($r)        => Limit::perMinute(60)->by($r->ip()));
        RateLimiter::for('api-nfc-resolve',fn ($r)        => Limit::perMinute(20)->by($r->ip()));
        RateLimiter::for('api-qr',         fn ($r)        => Limit::perMinute(30)->by($r->ip()));
        RateLimiter::for('api-upload',     fn ($r)        => Limit::perMinute(10)->by(optional($r->user())->id ?: $r->ip()));
        RateLimiter::for('api-notif-poll', fn ($r)        => Limit::perMinute(120)->by(optional($r->user())->id ?: $r->ip()));
        RateLimiter::for('api-attendance', fn ($r)        => Limit::perMinute(10)->by(optional($r->user())->id ?: $r->ip()));

        RateLimiter::for('web', fn (Request $r) => Limit::perMinute(120)->by($r->user()?->id ?: $r->ip()));

        RateLimiter::for('login', function (Request $r) {
            $key = Str::lower((string) $r->input('email')) . '|' . $r->ip();
            return [
                Limit::perMinute(5)->by('login:' . $key)->response(fn () =>
                    back()->withErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.'])
                ),
                Limit::perDay(50)->by('login-day:' . $r->ip()),
            ];
        });

        RateLimiter::for('password-reset', fn (Request $r) => [
            Limit::perMinute(3)->by('pw-reset:' . $r->ip()),
            Limit::perDay(10)->by('pw-reset-day:' . $r->ip()),
        ]);

        RateLimiter::for('nfc-resolve', function (Request $r) {
            $ip   = $r->ip();
            $sess = (string) $r->input('session_token', 'unknown');
            return [
                Limit::perMinute(30)->by('nfc-ip:' . $ip),
                Limit::perMinute(5)->by('nfc-sess:' . $sess),
                Limit::perMinute(3)->by('nfc-burst:' . $ip),
            ];
        });
        RateLimiter::for('nfc-ping', fn (Request $r) => Limit::perMinute(120)->by('ping:' . $r->ip()));

        RateLimiter::for('account-check', function (Request $r) {
            $method     = (string) $r->input('method', 'unknown');
            $value      = (string) $r->input('value', '');
            $identifier = hash_hmac('sha256', $value, config('app.key'));
            $ip         = $r->ip();
            return [
                Limit::perMinute(30)->by('check-ip:' . $ip),
                Limit::perMinute(5)->by('check-id:' . $identifier),
                Limit::perMinute(15)->by('check-ipm:' . $ip . ':' . $method),
                Limit::perMinute(2)->by('check-burst:' . $ip),
            ];
        });

        RateLimiter::for('uploads', fn (Request $r) => [
            Limit::perMinute(10)->by('upload:' . ($r->user()?->id ?: $r->ip())),
            Limit::perDay(100)->by('upload-day:' . ($r->user()?->id ?: $r->ip())),
        ]);

        RateLimiter::for('notifications-poll',   fn (Request $r) => Limit::perMinute(20)->by('notif-poll:' . $r->user()?->id));
        RateLimiter::for('notifications-action', fn (Request $r) => Limit::perMinute(60)->by('notif-act:' . $r->user()?->id));

        RateLimiter::for('exports', fn (Request $r) => Limit::perMinute(5)->by('export:' . $r->user()?->id));

        RateLimiter::for('webhook', fn (Request $r) => Limit::perMinute(300)->by('webhook:' . $r->ip()));
        RateLimiter::for('health',  fn () => Limit::none());

        RateLimiter::for('search', fn (Request $r) => [
            Limit::perMinute(30)->by('search:' . ($r->user()?->id ?: $r->ip())),
            Limit::perMinute(5)->by('search-burst:' . $r->ip()),
        ]);

        RateLimiter::for('booking-submit', fn (Request $r) => [
            Limit::perMinute(5)->by('booking:' . $r->user()?->id),
            Limit::perHour(20)->by('booking-hour:' . $r->user()?->id),
        ]);

        RateLimiter::for('contact', fn (Request $r) => [
            Limit::perMinute(2)->by('contact:' . $r->ip()),
            Limit::perDay(10)->by('contact-day:' . $r->ip()),
        ]);
    }

    /* ==============================================================
     | 9. BLADE DIRECTIVES
     * ============================================================== */
    protected function configureBladeDirectives(): void
    {
        Blade::if('role',    fn (string $role)   => Auth::check() && Auth::user()->hasRole($role));
        Blade::if('anyrole', fn (array $roles)   => Auth::check() && Auth::user()->hasAnyRole($roles));
        Blade::if('hasaccess', fn (string $ab)  => Auth::check() && Gate::allows($ab));
        Blade::if('feature', fn (string $feat)   => (bool) config("features.{$feat}", false));
        Blade::if('env_is',  fn (string $env)    => app()->environment($env));
        Blade::if('impersonating', fn ()         => request()->routeIs('owner.impersonate.*'));

        Blade::if('active', function (string ...$patterns) {
            foreach ($patterns as $p) {
                if (request()->routeIs($p)) return true;
            }
            return false;
        });

        Blade::if('once', function (string $key) {
            static $seen = [];
            if (isset($seen[$key])) return false;
            $seen[$key] = true;
            return true;
        });

        Blade::directive('money',    fn ($expr) => "<?php echo 'Rp ' . number_format({$expr}, 0, ',', '.'); ?>");
        Blade::directive('datetime', fn ($expr) => "<?php echo ({$expr})?->translatedFormat('d M Y, H:i'); ?>");
        Blade::directive('timeago',  fn ($expr) => "<?php echo ({$expr})?->diffForHumans(); ?>");
        Blade::directive('csrfMeta', fn ()      => '<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">');
    }

    /* ==============================================================
     | 10. FORCE HTTPS
     * ============================================================== */
    protected function configureUrlForceHttps(): void
    {
        if ($this->app->isProduction() && config('app.force_https', true)) {
            URL::forceScheme('https');
        }

        if (config('app.trust_proxies', false)) {
            Request::setTrustedProxies(
                ['*'],
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
            );
        }
    }

    /* ==============================================================
     | 11. NOTIFICATION MACROS
     * ============================================================== */
    protected function configureNotificationMacros(): void
    {
        NotificationFacade::macro('sendToRole', function (string|array $roles, $notification) {
            $users = User::role($roles)->where('is_active', true)->get();
            foreach ($users as $user) {
                $user->notify(clone $notification);
            }
            return $users->count();
        });

        NotificationFacade::macro('sendToPermission', function (string $permission, $notification) {
            $users = User::permission($permission)->where('is_active', true)->get();
            foreach ($users as $user) {
                $user->notify(clone $notification);
            }
            return $users->count();
        });

        NotificationFacade::macro('sendToUsers', function (array $userIds, $notification) {
            $users = User::whereIn('id', $userIds)->where('is_active', true)->get();
            foreach ($users as $user) {
                $user->notify(clone $notification);
            }
            return $users->count();
        });
    }

    /* ==============================================================
     | 12. VIEW COMPOSERS
     * ============================================================== */
    protected function configureViewComposers(): void
    {
        View::composer('partials.roles.owner.header', function ($view) {
            $pendingBookings = Cache::remember(
                'header.pending_bookings',
                now()->addMinutes(2),
                fn () => Booking::where('status', 'Menunggu Persetujuan')->count()
            );
            $view->with('pendingBookings', $pendingBookings);
        });

        View::composer([
            'partials.roles.*.header',
            'partials.header',
            'layouts.app',
            'layouts.owner',
            'layouts.admin',
            'layouts.roles.anggota-ukm',
            'layouts.roles.*',
        ], function ($view) {
            $user = function_exists('effective_user') ? effective_user() : Auth::user();
            if (! $user) return;

            $view->with([
                'authUser'     => $user,
                'unreadNotifs' => Cache::remember(
                    "user.{$user->id}.unread_notifs",
                    now()->addSeconds(30),
                    fn () => $user->unreadNotifications()->count()
                ),
                'userRole'  => $user->roles->pluck('name')->first() ?? 'member',
                'userLevel' => $user->level_id ?? 0,
            ]);
        });

        View::composer(
            ['partials.sidebar', 'partials.roles.*.sidebar'],
            SidebarComposer::class
        );

        View::composer('owner.notifications.*', function ($view) {
            $user = Auth::user();
            if (! $user) return;

            $view->with('notificationCounts', [
                'unread'  => $user->unreadNotifications()->count(),
                'starred' => $user->notifications()->whereNotNull('starred_at')->count(),
                'action'  => $user->notifications()
                    ->where('requires_action', true)
                    ->whereNull('actioned_at')
                    ->count(),
            ]);
        });
    }

    /* ==============================================================
     | 13. GLOBAL VIEW DATA
     * ============================================================== */
    protected function configureGlobalViewData(): void
    {
        View::composer('*', function ($view) {
            $pendingPayments         = 0;
            $criticalAttendanceCount = 0;

            foreach ([
                \App\Models\Financial\PaymentAllocation::class,
                \App\Models\PaymentAllocation::class,
            ] as $class) {
                if (! class_exists($class)) continue;
                try {
                    $pendingPayments = $class::query()->where('status', 'pending')->count();
                    break;
                } catch (Throwable $e) {
                    // silent
                }
            }

            if (class_exists(\App\Models\AttendanceAuditAlert::class)) {
                try {
                    $criticalAttendanceCount = \App\Models\AttendanceAuditAlert::query()
                        ->where('severity', 'critical')
                        ->whereNull('resolved_at')
                        ->count();
                } catch (Throwable $e) {
                    // silent
                }
            }

            $view->with([
                'pending_payments'          => $pendingPayments,
                'critical_attendance_count' => $criticalAttendanceCount,
                'isImpersonating'           => request()->routeIs('owner.impersonate.*'),
            ]);
        });
    }

    /* ==============================================================
     | 14. SLOW QUERY LOGGER
     * ============================================================== */
    protected function configureSlowQueryLogger(): void
    {
        if (! $this->app->isLocal() && ! config('app.debug')) return;
        if (! config('app.log_slow_query', false)) return;

        $thresholdMs = (int) config('app.slow_query_ms', 500);

        DB::listen(function ($query) use ($thresholdMs) {
            if ($query->time < $thresholdMs) return;
            try {
                Log::channel('performance')->warning('Slow Query Detected', [
                    'sql'      => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms'  => $query->time,
                    'url'      => request()->fullUrl(),
                    'user_id'  => Auth::id(),
                ]);
            } catch (Throwable $e) {
                // silent
            }
        });
    }

    /* ==============================================================
     | 15. LOGGING CONTEXT
     * ============================================================== */
    protected function configureLogging(): void
    {
        try {
            if (! $this->app->runningInConsole()) {
                Log::withContext([
                    'user_id' => Auth::id(),
                    'ip'      => request()->ip(),
                    'url'     => request()->fullUrl(),
                ]);
            }
        } catch (Throwable $e) {
            // ignore
        }
    }

    /* ==============================================================
     | 16. HEALTH CHECKS
     * ============================================================== */
    protected function configureHealthChecks(): void
    {
        if (! class_exists(\Spatie\Health\Facades\Health::class)) return;

        \Spatie\Health\Facades\Health::checks([
            \Spatie\Health\Checks\Checks\DatabaseCheck::new(),
            \Spatie\Health\Checks\Checks\CacheCheck::new(),
            \Spatie\Health\Checks\Checks\DebugModeCheck::new(),
            \Spatie\Health\Checks\Checks\EnvironmentCheck::new(),
            \Spatie\Health\Checks\Checks\OptimizedAppCheck::new(),
            \Spatie\Health\Checks\Checks\ScheduleCheck::new(),
        ]);
    }
}