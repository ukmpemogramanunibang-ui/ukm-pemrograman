<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    \App\Models\AttendancePenalty::class => \App\Policies\AttendancePenaltyPolicy::class,
    \App\Models\AuditLog::class => \App\Policies\AuditLogPolicy::class,
];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-secretary-dashboard', function (User $user) {
            // Debug: cek level_id
            // \Log::info('User level: ' . $user->level_id);

            if (in_array($user->level_id, [1, 3])) {
                return true;
            }

            if ($user->level_id == 4) {
                return $user->jabatans()->whereIn('name', ['Sekretaris', 'Asisten Sekretaris'])->exists();
            }

            return false;
        });
    }
    
}