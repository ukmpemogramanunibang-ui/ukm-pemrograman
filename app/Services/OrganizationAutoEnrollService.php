<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Auto-enroll user ke organisasi default.
 * Dipanggil di UserObserver::created() atau setelah registrasi.
 */
class OrganizationAutoEnrollService
{
    /** Slug organisasi default untuk auto-enroll. */
    private const DEFAULT_SLUG = 'ukm-pemrograman-unicode';

    /**
     * Auto-enroll user baru ke organisasi default.
     * Idempotent — aman dipanggil berkali-kali.
     */
    public static function autoEnroll(User $user): ?OrganizationMembership
    {
        $defaultOrg = Organization::where('slug', self::DEFAULT_SLUG)
            ->where('status', 'active')
            ->first();

        if (!$defaultOrg) {
            Log::warning('[AutoEnroll] Organisasi default tidak ditemukan.', [
                'slug' => self::DEFAULT_SLUG,
            ]);
            return null;
        }

        // Mapping level_id → access_role default
        $roleMap = [
            3 => 'leader',   // Ketua UKM
            4 => 'officer',  // Pengurus
            5 => 'member',   // Anggota
            6 => 'member',   // Anggota Umum (viewer)
            7 => 'pj',       // PJ
        ];

        $accessRole = $roleMap[(int) $user->level_id] ?? 'member';

        $membership = OrganizationMembership::updateOrCreate(
            [
                'organization_id' => $defaultOrg->id,
                'user_id'         => $user->id,
            ],
            [
                'access_role'       => $accessRole,
                'membership_status' => $user->is_active ? 'active' : 'inactive',
                'joined_at'         => $user->created_at ?? now(),
            ]
        );

        Log::info('[AutoEnroll] User auto-enrolled.', [
            'user_id' => $user->id,
            'org'     => $defaultOrg->slug,
            'role'    => $accessRole,
        ]);

        return $membership;
    }

    /**
     * Sync semua user aktif yang belum punya membership.
     * Jalankan via command atau manual.
     */
    public static function syncAll(): int
    {
        $defaultOrg = Organization::where('slug', self::DEFAULT_SLUG)->first();
        if (!$defaultOrg) return 0;

        $count = 0;
        User::whereNull('deleted_at')
            ->whereNotIn('level_id', [1, 2, 8]) // skip owner/superadmin/guest
            ->whereDoesntHave('organizationMemberships', function ($q) use ($defaultOrg) {
                $q->where('organization_id', $defaultOrg->id);
            })
            ->chunkById(200, function ($users) use (&$count) {
                foreach ($users as $user) {
                    if (self::autoEnroll($user)) $count++;
                }
            });

        return $count;
    }
}