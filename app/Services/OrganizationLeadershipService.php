<?php

namespace App\Services;

use App\Models\OrganizationMembership;
use Illuminate\Support\Facades\DB;

class OrganizationLeadershipService
{
    /**
     * Promote user ke leader — pastikan hanya 1 leader aktif per organisasi.
     */
    public static function promoteToLeader(int $organizationId, int $userId, ?string $positionLabel = null): void
    {
        DB::transaction(function () use ($organizationId, $userId, $positionLabel) {
            $existing = OrganizationMembership::where('organization_id', $organizationId)
                ->where('access_role', 'leader')
                ->where('membership_status', 'active')
                ->where('user_id', '!=', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new \RuntimeException(
                    "Sudah ada leader aktif di organisasi ini (user_id={$existing->user_id}). Transfer dulu jabatannya."
                );
            }

            OrganizationMembership::updateOrCreate(
                ['organization_id' => $organizationId, 'user_id' => $userId],
                [
                    'access_role'       => 'leader',
                    'membership_status' => 'active',
                    'position_label'    => $positionLabel ?? 'Ketua',
                    'joined_at'         => now(),
                ]
            );
        });
    }

    public static function transferLeadership(int $organizationId, int $fromUserId, int $toUserId): void
    {
        DB::transaction(function () use ($organizationId, $fromUserId, $toUserId) {
            OrganizationMembership::where('organization_id', $organizationId)
                ->where('user_id', $fromUserId)->where('access_role', 'leader')
                ->update(['access_role' => 'officer', 'updated_at' => now()]);

            OrganizationMembership::where('organization_id', $organizationId)
                ->where('user_id', $toUserId)
                ->update(['access_role' => 'leader', 'updated_at' => now()]);
        });
    }
}