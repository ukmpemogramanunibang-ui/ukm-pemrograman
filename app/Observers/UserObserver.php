<?php

namespace App\Observers;

use App\Models\User;
use App\Services\DataProtectionService;
use App\Services\QrRecoveryService;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;   // 🔥 WAJIB

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
        $this->syncRoleByLevel($user);   // ← assign role saat user baru dibuat
    }

    public function updated(User $user): void
    {
        if ($user->isDirty('level_id')) {
            $this->syncRoleByLevel($user);
        }
    }

    public function deleted(User $user): void
    {
        $this->syncUser($user);
    }

    public function restored(User $user): void
    {
        $this->syncUser($user);
        $this->syncRoleByLevel($user);
    }

    public function saved(User $user): void
    {
        app(QrRecoveryService::class)->generateSnapshot($user);
    }

    /**
     * Auto-assign Spatie role berdasarkan level_id.
     */
    protected function syncRoleByLevel(User $user): void
    {
        $map = [
            1 => 'owner',
            2 => 'superadmin',
            3 => 'ketua',
            4 => 'pengurus',
            5 => 'anggota',
            6 => 'anggota umum',
            7 => 'pengunjung',
        ];

        $roleName = $map[(int) $user->level_id] ?? null;

        if ($roleName && Role::where('name', $roleName)->exists()) {
            try {
                $user->syncRoles([$roleName]);
            } catch (\Throwable $e) {
                Log::warning('[UserObserver] syncRole gagal', [
                    'user_id' => $user->id,
                    'role'    => $roleName,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    protected function syncUser(User $user): void
    {
        try {
            $this->protection->syncUser($user);
            Log::info('User auto-synced to protected DB', ['user_uuid' => $user->user_uuid]);
        } catch (\Exception $e) {
            Log::error('Auto-sync user failed', [
                'user_uuid' => $user->user_uuid,
                'error'     => $e->getMessage()
            ]);
        }
    }
}