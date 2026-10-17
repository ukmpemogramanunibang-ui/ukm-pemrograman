<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\PointHistory;
use App\Models\UserQrCode;
use App\Observers\UserObserver;
use App\Observers\PointHistoryObserver;
use App\Observers\UserQrCodeObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Illuminate\Auth\Events\Registered::class => [
            \App\Listeners\CreateUserRecoverySnapshot::class,
        ],
        \App\Events\UserUpdated::class => [
            \App\Listeners\UpdateUserRecoverySnapshot::class,
        ],
    ];

    public function boot(): void
    {
        // Daftarkan observer untuk auto-sync ke protected database
        User::observe(UserObserver::class);
        PointHistory::observe(PointHistoryObserver::class);
        UserQrCode::observe(UserQrCodeObserver::class);
    }
}

