<?php

namespace App\Observers;

use App\Models\User;
use App\Services\DataProtectionService;
use Illuminate\Support\Facades\Log;
use App\Services\QrRecoveryService;

class UserObserver
{
    protected DataProtectionService $protection;

    public function __construct(DataProtectionService $protection)
    {
        $this->protection = $protection;
    }

    public function created(User $user): void
    {
        $this->syncUser($user);
    }

    public function updated(User $user): void
    {
        $this->syncUser($user);
    }

    public function deleted(User $user): void
    {
        $this->syncUser($user);
    }

    public function restored(User $user): void
    {
        $this->syncUser($user);
    }
public function saved(User $user)
    {
        app(QrRecoveryService::class)->generateSnapshot($user);
    }

    protected function syncUser(User $user): void
    {
        try {
            $this->protection->syncUser($user);
            Log::info('User auto-synced to protected DB', ['user_uuid' => $user->user_uuid]);
        } catch (\Exception $e) {
            Log::error('Auto-sync user failed', [
                'user_uuid' => $user->user_uuid,
                'error' => $e->getMessage()
            ]);
        }
    }
}