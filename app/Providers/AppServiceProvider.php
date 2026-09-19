<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Booking;
use App\Models\Financial\PaymentAllocation;
use App\Observers\PaymentAllocationObserver;
use Illuminate\Support\Facades\Gate;
use App\Policies\AttendanceSessionPolicy;
use App\Models\AttendanceSession;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
class AppServiceProvider extends ServiceProvider

{
    public function register(): void
    {
        //
    }

public function boot(): void
{
    Gate::policy(AttendanceSession::class, AttendanceSessionPolicy::class);
    \App\Models\AttendanceRecord::observe(\App\Observers\AttendanceRecordObserver::class);
    PaymentAllocation::observe(PaymentAllocationObserver::class);

    View::composer('partials.roles.owner.header', function ($view) {
        $pendingBookings = Booking::where('status', 'Menunggu Persetujuan')->count();
        $view->with('pendingBookings', $pendingBookings);
    });

    /* ============================================================
     | MULTI-LAYER RATE LIMITER – CEK AKUN UNICODE
     * ============================================================ */
    RateLimiter::for('account-check', function (Request $request) {
        $method     = (string) $request->input('method', 'unknown');
        $value      = (string) $request->input('value', '');
        $identifier = hash_hmac('sha256', $value, config('app.key'));
        $ip         = $request->ip();

        return [
            // Layer 1: per IP (cegah scraping massal)
            Limit::perMinute(30)->by('check-ip:' . $ip),

            // Layer 2: per identifier (cegah enumerasi NIM yang sama)
            Limit::perMinute(5)->by('check-id:' . $identifier),

            // Layer 3: per IP + method (cegah abuse satu method)
            Limit::perMinute(15)->by('check-ipm:' . $ip . ':' . $method),

            // Layer 4: burst protection
            Limit::perMinute(2)->by('check-burst:' . $ip),
        ];
    });
}
}
