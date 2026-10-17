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
    /**
     * Abilities yang boleh di-bypass oleh Superadmin.
     * JANGAN pakai wildcard — superadmin TIDAK boleh bypass
     * operasi destruktif (force-delete, impersonate, dsb).
     */
    private const SUPERADMIN_BYPASS = [
        'view-*',
        'manage-users',
        'manage-roles',
        'manage-attendance',
        'manage-lms',
        'approve-*',
        'view-financial-reports',
        'view-audit-log',
    ];

    /* ==============================================================
     | REGISTER
     * ============================================================== */
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

    /**
     * Route pattern global — dipakai lintas file route.
     * Di sini supaya konsisten, tidak perlu ulang where() di mana-mana.
     */
    protected function registerRoutePatterns(): void
    {
        Route::pattern('uuid', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}');
        Route::pattern('nim',  '[0-9]{6,15}');
        Route::pattern('slug', '[a-z0-9]+(?:-[a-z0-9]+)*');
        Route::pattern('token', '[A-Za-z0-9_\-]{16,}');
        Route::pattern('year', '[12][0-9]{3}');
    }

    /* ==============================================================
     | BOOT
     * ============================================================== */
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
        \App\Models\DashboardItem::observe(\App\Observers\DashboardItemObserver::class);
    }

    /** Jalankan method, tapi jangan sampai error satu method mematikan boot. */
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

    /* ==============================================================
     | 1. STRICT MODE
     * ============================================================== */
    protected function configureStrictMode(): void
    {
        if ($this->app->isProduction()) {
            if (config('app.prevent_lazy_loading', false)) {
                Model::preventLazyLoading();
            }
            Relation::requireMorphMap();
            return;
        }

        // Non-production: strict tapi tidak saat unit test (banyak fixture)
        if (! $this->app->runningUnitTests()) {
            Model::shouldBeStrict(true);
            Model::preventLazyLoading(true);
            Model::preventSilentlyDiscardingAttributes(true);
            // ⚠️ preventAccessingMissingAttributes sering false-positive
            //    karena accessor dinamis. Aktifkan hanya saat debugging.
            if (config('app.strict_missing_attributes', false)) {
                Model::preventAccessingMissingAttributes(true);
            }
        }

        // Lazy loading violation → log informatif (bukan crash)
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            Log::warning('[LazyLoading] N+1 terdeteksi', [
                'model'    => get_class($model),
                'relation' => $relation,
                'url'      => request()?->fullUrl(),
                'user_id'  => Auth::id(),
            ]);
        });
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
     | 3. MODEL CONFIG
     * ============================================================== */
    protected function configureModelConfig(): void
    {
        // Guard mass-assignment aktif
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
        /*
         * ⚠️ Superadmin bypass HANYA pada ability yang masuk whitelist.
         *    Jangan biarkan return true untuk semua — itu bug keamanan
         *    klasik yang membuat superadmin bisa melakukan force-delete,
         *    impersonate Owner, dsb.
         */
        Gate::before(function (User $user, string $ability) {
            if (! $user->hasRole('superadmin')) {
                return null;
            }

            foreach (self::SUPERADMIN_BYPASS as $pattern) {
                if (Str::is($pattern, $ability)) {
                    return true;
                }
            }

            return null; // fallthrough ke policy/gate asli
        });

        // Area
        Gate::define('access-owner-area',  fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']) && $u->is_active);
        Gate::define('access-admin-area',  fn (User $u) => $u->hasAnyRole(['admin', 'owner', 'superadmin']) && $u->is_active);
        Gate::define('access-member-area', fn (User $u) => (bool) $u->is_active);

        // Management
        Gate::define('manage-system-settings', fn (User $u) => $u->hasRole('superadmin'));
        Gate::define('manage-users',           fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('manage-roles',           fn (User $u) => $u->hasRole('superadmin'));
        Gate::define('promote-demote',         fn (User $u) => $u->hasRole('owner'));

        // Financial
        Gate::define('view-financial-reports', fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'finance']));
        Gate::define('approve-payments',       fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'finance']));

        // Sensitive
        Gate::define('export-sensitive-data', fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('view-audit-log',        fn (User $u) => $u->hasAnyRole(['owner', 'superadmin']));
        Gate::define('force-delete',          fn (User $u) => $u->hasRole('owner')); // ❗bukan superadmin

        // Operasional
        Gate::define('approve-booking',   fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'admin']));
        Gate::define('manage-attendance', fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'admin']));
        Gate::define('manage-lms',        fn (User $u) => $u->hasAnyRole(['owner', 'superadmin', 'admin']));
    }

    /* ==============================================================
     | 6. OBSERVERS
     * ============================================================== */
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
            if (! class_exists($observer)) {
                Log::warning("[AppServiceProvider] Observer tidak ditemukan: {$observer}");
                continue;
            }
            if (! class_exists($model)) {
                Log::warning("[AppServiceProvider] Model tidak ditemukan: {$model}");
                continue;
            }
            try {
                $model::observe($observer);
            } catch (Throwable $e) {
                Log::error("[AppServiceProvider] Gagal register observer", [
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

        Validator::extend('phone_id', fn ($a, $v) =>
            preg_match('/^(\+62|62|0)8[1-9][0-9]{6,11}$/', (string) $v) === 1,
            'Format nomor HP tidak valid (contoh: 08123456789)'
        );

        Validator::extend('nim', fn ($a, $v) =>
            preg_match('/^[0-9]{8,15}$/', (string) $v) === 1,
            'Format NIM tidak valid'
        );

        Validator::extend('slug', fn ($a, $v) =>
            preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $v) === 1,
            'Format slug tidak valid'
        );

        Validator::extend('clean_text', function ($a, $v) {
            $banned = ['<script', 'javascript:', 'onerror=', 'onload=', 'data:text/html'];
            $lower  = Str::lower((string) $v);
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
        // ---- API ----
        RateLimiter::for('api',              fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-ping',         fn ($r)        => Limit::perMinute(120)->by($r->ip()));
        RateLimiter::for('api-login',        fn ($r)        => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('api-auth',         fn ($r)        => Limit::perMinute(300)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-legacy',       fn ($r)        => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-nfc-ping',     fn ($r)        => Limit::perMinute(60)->by($r->ip()));
        RateLimiter::for('api-nfc-resolve',  fn ($r)        => Limit::perMinute(20)->by($r->ip()));
        RateLimiter::for('api-qr',           fn ($r)        => Limit::perMinute(30)->by($r->ip()));
        RateLimiter::for('api-upload',       fn ($r)        => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-notif-poll',   fn ($r)        => Limit::perMinute(120)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('api-attendance',   fn ($r)        => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));

        // ---- Web ----
        RateLimiter::for('web', fn (Request $r) => Limit::perMinute(120)->by($r->user()?->id ?: $r->ip()));

        // ---- Login ----
        RateLimiter::for('login', function (Request $r) {
            $key = Str::lower((string) $r->input('email')) . '|' . $r->ip();
            return [
                Limit::perMinute(5)->by('login:' . $key)->response(fn () =>
                    back()->withErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.'])
                ),
                Limit::perDay(50)->by('login-day:' . $r->ip()),
            ];
        });

        // ---- Password reset ----
        RateLimiter::for('password-reset', fn (Request $r) => [
            Limit::perMinute(3)->by('pw-reset:' . $r->ip()),
            Limit::perDay(10)->by('pw-reset-day:' . $r->ip()),
        ]);

        // ---- NFC ----
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

        // ---- Account check ----
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

        // ---- Uploads & Exports ----
        RateLimiter::for('uploads', fn (Request $r) => [
            Limit::perMinute(10)->by('upload:' . ($r->user()?->id ?: $r->ip())),
            Limit::perDay(100)->by('upload-day:' . ($r->user()?->id ?: $r->ip())),
        ]);
        RateLimiter::for('exports', fn (Request $r) => Limit::perMinute(5)->by('export:' . $r->user()?->id));

        // ---- Notifications ----
        RateLimiter::for('notifications-poll',   fn (Request $r) => Limit::perMinute(20)->by('notif-poll:' . $r->user()?->id));
        RateLimiter::for('notifications-action', fn (Request $r) => Limit::perMinute(60)->by('notif-act:' . $r->user()?->id));

        // ---- Webhooks & Health ----
        RateLimiter::for('webhook', fn (Request $r) => Limit::perMinute(300)->by('webhook:' . $r->ip()));
        RateLimiter::for('health',  fn () => Limit::none());

        // ---- Search ----
        RateLimiter::for('search', fn (Request $r) => [
            Limit::perMinute(30)->by('search:' . ($r->user()?->id ?: $r->ip())),
            Limit::perMinute(5)->by('search-burst:' . $r->ip()),
        ]);

        // ---- Booking ----
        RateLimiter::for('booking-submit', fn (Request $r) => [
            Limit::perMinute(5)->by('booking:' . $r->user()?->id),
            Limit::perHour(20)->by('booking-hour:' . $r->user()?->id),
        ]);

        // ---- Contact ----
        RateLimiter::for('contact', fn (Request $r) => [
            Limit::perMinute(2)->by('contact:' . $r->ip()),
            Limit::perDay(10)->by('contact-day:' . $r->ip()),
        ]);

        // ---- 🔥 Owner actions (baru) ----
        RateLimiter::for('owner-actions', fn (Request $r) => [
            Limit::perMinute(30)->by('owner-act:' . $r->user()?->id),
            Limit::perMinute(5)->by('owner-act-burst:' . $r->user()?->id),
        ]);
        RateLimiter::for('owner-promote-demote', fn (Request $r) => [
            Limit::perMinute(10)->by('promote:' . $r->user()?->id),
            Limit::perHour(50)->by('promote-hour:' . $r->user()?->id),
        ]);
    }

    /* ==============================================================
     | 9. BLADE DIRECTIVES
     * ============================================================== */
    protected function configureBladeDirectives(): void
    {
        // ---- Auth ----
        Blade::if('role',        fn (string $role)  => Auth::check() && Auth::user()->hasRole($role));
        Blade::if('anyrole',     fn (array $roles)  => Auth::check() && Auth::user()->hasAnyRole($roles));
        Blade::if('permission',  fn (string $perm)  => Auth::check() && Auth::user()->can($perm));
        Blade::if('hasaccess',   fn (string $ab)    => Auth::check() && Gate::allows($ab));
        Blade::if('impersonating', fn ()            => request()->routeIs('owner.impersonate.*'));

        // ---- Level ----
        Blade::if('haslevel', function (int|array $levels) {
            if (! Auth::check()) return false;
            $current = (int) (Auth::user()->level_id ?? 0);
            return is_array($levels) ? in_array($current, $levels, true) : $current === (int) $levels;
        });

        // ---- Feature / Env ----
        Blade::if('feature', fn (string $feat) => (bool) config("features.{$feat}", false));
        Blade::if('env_is',  fn (string $env)  => app()->environment($env));

        // ---- Active route ----
        Blade::if('active', function (string ...$patterns) {
            foreach ($patterns as $p) {
                if (request()->routeIs($p)) return true;
            }
            return false;
        });

        // ---- once_key ----
        Blade::if('once_key', function (string $key) {
            static $seen = [];
            if (isset($seen[$key])) return false;
            $seen[$key] = true;
            return true;
        });

        // ---- Directives ----
        Blade::directive('money',    fn ($expr) => "<?php echo 'Rp ' . number_format({$expr}, 0, ',', '.'); ?>");
        Blade::directive('datetime', fn ($expr) => "<?php echo ({$expr})?->translatedFormat('d M Y, H:i'); ?>");
        Blade::directive('timeago',  fn ($expr) => "<?php echo ({$expr})?->diffForHumans(); ?>");
        Blade::directive('csrfMeta', fn ()      => '<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">');
        Blade::directive('viteNonce', fn ()     => "<?php echo e(\\Illuminate\\Support\\Facades\\Vite::cspNonce() ?? ''); ?>");
    }

    /* ==============================================================
     | 10. HTTPS & PROXIES
     * ============================================================== */
    protected function configureUrlForceHttps(): void
    {
        if ($this->app->isProduction() && config('app.force_https', true)) {
            URL::forceScheme('https');
        }

        if (config('app.trust_proxies', false)) {
            Request::setTrustedProxies(
                ['*'],
                Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
            );
        }

        // Route default parameter (dipakai di URL::route jika tidak di-set)
        URL::defaults([
            'locale' => app()->getLocale(),
        ]);
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
            $view->with('pendingBookings', Cache::remember(
                'header.pending_bookings',
                now()->addMinutes(2),
                fn () => Booking::where('status', 'Menunggu Persetujuan')->count()
            ));
        });

        View::composer([
            'partials.roles.*.header',
            'partials.header',
            'layouts.app',
            'layouts.owner',
            'layouts.admin',
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
     | 13. GLOBAL VIEW DATA (di-cache agar tidak query tiap view)
     * ============================================================== */
    protected function configureGlobalViewData(): void
    {
        // Hanya bind variabel global; query di-cache 60 detik.
        View::composer('*', function ($view) {
            $view->with([
                'pending_payments'          => $this->safeCount(fn () =>
                    (int) Cache::remember('global.pending_payments', 60, function () {
                        // Hitung dari invoice jika ada
                        $inv = \App\Models\Financial\Invoice::class;
                        if (class_exists($inv)) {
                            try { return $inv::where('status', 'pending')->count(); }
                            catch (Throwable $e) { return 0; }
                        }
                        return 0;
                    })
                ),
                'critical_attendance_count' => $this->safeCount(fn () =>
                    (int) Cache::remember('global.critical_attendance', 60, function () {
                        $cls = \App\Models\AttendanceAuditAlert::class;
                        if (class_exists($cls)) {
                            try {
                                return $cls::query()
                                    ->where('severity', 'critical')
                                    ->whereNull('resolved_at')
                                    ->count();
                            } catch (Throwable $e) { return 0; }
                        }
                        return 0;
                    })
                ),
                'isImpersonating' => request()->routeIs('owner.impersonate.*'),
            ]);
        });
    }

    /** Bungkus operasi count, return 0 jika error. */
    protected function safeCount(callable $fn): int
    {
        try { return (int) $fn(); }
        catch (Throwable $e) { return 0; }
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
                    'request_id' => request()->header('X-Request-ID') ?? app('request_id') ?? null,
                ]);
            } catch (Throwable $e) { /* silent */ }
        });
    }

    /* ==============================================================
     | 15. LOGGING CONTEXT
     * ============================================================== */
    protected function configureLogging(): void
    {
        try {
            if ($this->app->runningInConsole()) return;

            // Generate request_id sekali per request → untuk tracing
            $requestId = request()->header('X-Request-ID')
                ?? (string) Str::uuid();
            app()->instance('request_id', $requestId);

            Log::withContext([
                'request_id' => $requestId,
                'user_id'    => Auth::id(),
                'ip'         => request()->ip(),
                'url'        => request()->fullUrl(),
                'method'     => request()->method(),
            ]);
        } catch (Throwable $e) { /* ignore */ }
    }

    /* ==============================================================
     | 16. HEALTH CHECKS
     * ============================================================== */
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
        } catch (Throwable $e) {
            Log::warning('[Health] Gagal register checks', ['error' => $e->getMessage()]);
        }
    }

    /* ==============================================================
     | 17. MACROS (Blueprint, Collection, Str, Number)
     * ============================================================== */
    protected function configureMacros(): void
    {
        // ---- Blueprint ----
        Blueprint::macro('uuidPrimary', function () {
            /** @var Blueprint $this */
            $this->uuid('id')->primary();
        });

        Blueprint::macro('userstamps', function () {
            /** @var Blueprint $this */
            $this->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Blueprint::macro('timestampsTz', function () {
            /** @var Blueprint $this */
            $this->timestampTz('created_at')->nullable()->useCurrent();
            $this->timestampTz('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

        Blueprint::macro('softDeletesTz', function ($column = 'deleted_at') {
            /** @var Blueprint $this */
            $this->timestampTz($column)->nullable();
        });

        // ---- Collection ----
        \Illuminate\Support\Collection::macro('toSelectOptions', function () {
            /** @var \Illuminate\Support\Collection $this */
            return $this->mapWithKeys(fn ($item) => [
                $item->id ?? $item['id'] => $item->name ?? $item['name'] ?? '',
            ])->toArray();
        });

        // ---- Str ----
        Str::macro('initials', function (string $name, int $max = 2): string {
            return collect(preg_split('/\s+/', trim($name)))
                ->filter()
                ->map(fn ($w) => mb_substr($w, 0, 1))
                ->take($max)
                ->implode('');
        });

        Str::macro('maskEmail', function (string $email): string {
            [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
            if (! $domain) return $email;
            $visible = mb_substr($local, 0, 2);
            return $visible . str_repeat('*', max(mb_strlen($local) - 2, 1)) . '@' . $domain;
        });
    }

    /* ==============================================================
     | 18. VITE CSP NONCE
     * ============================================================== */
    protected function configureViteNonce(): void
    {
        // CSP nonce dipakai bersama middleware SecurityHeaders.
        // Di sini hanya generate jika belum ada.
        try {
            if (! app()->bound('csp_nonce')) {
                app()->instance('csp_nonce', Str::random(32));
            }
        } catch (Throwable $e) { /* ignore */ }
    }

    /* ==============================================================
     | 19. PAGINATOR
     * ============================================================== */
    protected function configurePaginator(): void
    {
        BasePaginator::useBootstrapFive();
    }

    /* ==============================================================
     | 20. DATABASE GUARDS
     * ============================================================== */
    protected function configureDatabaseGuards(): void
    {
        // Cegah migrate:fresh / db:wipe di production (harus pakai flag --force eksplisit)
        if ($this->app->isProduction()) {
            DB::prohibitDestructiveCommands(true);
        }
    }

    /* ==============================================================
     | 21. QUEUE EVENTS (opsional, untuk monitoring)
     * ============================================================== */
    protected function configureQueueEvents(): void
    {
        if ($this->app->runningInConsole()) return;

        Queue::failing(function ($event) {
            Log::error('[Queue Failing]', [
                'connection' => $event->connectionName,
                'job'        => $event->job?->resolveName(),
                'exception'  => $event->exception->getMessage(),
            ]);
        });
    }
}