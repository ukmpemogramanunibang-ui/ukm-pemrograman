<?php

namespace App\Observers;

use App\Models\User;
use App\Services\DataProtectionService;
use App\Services\OrganizationAutoEnrollService;
use App\Services\QrRecoveryService;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserObserver
{
    /**
     * Level yang di-auto-assign Spatie role berdasarkan level_id.
     *
     * ⚠️ Diverifikasi terhadap data `roles` di DB:
     *    - 1 owner, 2 superadmin, 3 ketua, 4 pengurus inti,
     *      5 pj/assisten, 6 anggota kelompok, 7 anggota umum,
     *      8 pengunjung, 9 pengurus, 10 anggota, 11 guest
     *
     * Mapping level_id (dari LevelStatusSeeder):
     *    1 => owner
     *    2 => superadmin
     *    3 => ketua
     *    4 => pengurus inti
     *    5 => anggota
     *    6 => anggota umum
     *    7 => pj/assisten
     *    8 => guest
     */
    private const LEVEL_ROLE_MAP = [
        1 => 'owner',
        2 => 'superadmin',
        3 => 'ketua',
        4 => 'pengurus inti',
        5 => 'anggota',
        6 => 'anggota umum',
        7 => 'pj/assisten',
        8 => 'guest',
    ];

    /**
     * Level yang TIDAK di-auto-enroll ke organisasi:
     *   1 owner, 2 superadmin, 8 guest
     */
    private const SKIP_ORG_ENROLL_LEVELS = [1, 2, 8];

    /* ═══════════════════════════════════════════════════════
     | EVENTS — SATU METHOD created(), BUKAN DUA
     * ═══════════════════════════════════════════════════════ */

    public function created(User $user): void
    {
        $this->safe('syncUser',              fn () => $this->syncUser($user), $user);
        $this->safe('syncRoleByLevel',       fn () => $this->syncRoleByLevel($user), $user);
        $this->safe('autoEnrollOrganization', fn () => $this->autoEnrollOrganization($user), $user);
    }

    public function updated(User $user): void
    {
        if (!$user->isDirty('level_id')) {
            return;
        }

        $this->safe('syncRoleByLevel', fn () => $this->syncRoleByLevel($user), $user);
    }

    public function deleted(User $user): void
    {
        $this->safe('syncUser', fn () => $this->syncUser($user), $user);
    }

    public function restored(User $user): void
    {
        $this->safe('syncUser',        fn () => $this->syncUser($user), $user);
        $this->safe('syncRoleByLevel', fn () => $this->syncRoleByLevel($user), $user);
    }

    public function saved(User $user): void
    {
        if (empty($user->user_uuid)) return;

        $this->safe('generateQrSnapshot', function () use ($user) {
            app(QrRecoveryService::class)->generateSnapshot($user);
        }, $user);
    }

    /* ═══════════════════════════════════════════════════════
     | HELPERS — masing-masing tanggung jawab tunggal
     * ═══════════════════════════════════════════════════════ */

    private function safe(string $label, callable $action, User $user): void
    {
        try {
            $action();
        } catch (\Throwable $e) {
            Log::error("[UserObserver::{$label}] failed", [
                'user_id'   => $user->id,
                'user_uuid' => $user->user_uuid ?? null,
                'level_id'  => $user->level_id,
                'error'     => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
        }
    }

    private function syncUser(User $user): void
    {
        app(DataProtectionService::class)->syncUser($user);
        Log::info('[UserObserver] User synced to protected DB.', [
            'user_uuid' => $user->user_uuid,
        ]);
    }

    private function syncRoleByLevel(User $user): void
    {
        $roleName = self::LEVEL_ROLE_MAP[(int) $user->level_id] ?? null;
        if (!$roleName) return;

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            Log::warning('[UserObserver::syncRole] Role tidak ditemukan.', [
                'user_id' => $user->id,
                'role'    => $roleName,
            ]);
            return;
        }

        // Idempotent: skip kalau sudah punya
        if ($user->hasRole($roleName)) return;

        $user->syncRoles([$roleName]);
    }

    private function autoEnrollOrganization(User $user): void
    {
        if (in_array((int) $user->level_id, self::SKIP_ORG_ENROLL_LEVELS, true)) {
            return;
        }

        app(OrganizationAutoEnrollService::class)->autoEnroll($user);
    }
}