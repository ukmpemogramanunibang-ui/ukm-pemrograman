<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\AttendancePenalty;
use App\Models\AuditLog;
use App\Models\Lms\LmsGroup;
use App\Models\Lms\LmsItem;
use App\Models\Financial\FinancialCampaign;
use App\Policies\AttendancePenaltyPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\Lms\LmsGroupPolicy;
use App\Policies\Lms\LmsItemPolicy;
use App\Policies\Financial\FinancialCampaignPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        AttendancePenalty::class    => AttendancePenaltyPolicy::class,
        AuditLog::class             => AuditLogPolicy::class,
        LmsGroup::class             => LmsGroupPolicy::class,
        LmsItem::class              => LmsItemPolicy::class,
        FinancialCampaign::class    => FinancialCampaignPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // ─── Secretary dashboard ───
        Gate::define('access-secretary-dashboard', function (User $user): bool {
            if (in_array($user->level_id, [1, 3], true)) {
                return true;
            }

            if ($user->level_id === 4) {
                return $user->jabatans()
                    ->whereIn('name', ['Sekretaris', 'Asisten Sekretaris'])
                    ->exists();
            }

            return false;
        });

        // ─── Financial campaign abilities ───
        Gate::define('manage-campaigns', fn (User $user) => in_array($user->level_id, [1, 2, 3, 4], true));
        Gate::define('finalize-campaign', fn (User $user) => in_array($user->level_id, [1, 2, 3], true));
    }
}