<?php

namespace App\Services;

use App\Models\OrganizationMembership;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrganizationLeadershipService
{
    /**
     * Promote user ke leader — pastikan hanya 1 leader aktif per organisasi.
     */
    public static function promoteToLeader(
        int $organizationId,
        int $userId,
        ?string $positionLabel = null
    ): OrganizationMembership {
        return DB::transaction(function () use ($organizationId, $userId, $positionLabel) {

            // Lock semua leader aktif di org ini (cegah race condition)
            $existingLeader = OrganizationMembership::where('organization_id', $organizationId)
                ->where('access_role', 'leader')
                ->where('membership_status', 'active')
                ->lockForUpdate()
                ->first();

            // Idempotent: kalau user ini SUDAH leader, return apa adanya
            if ($existingLeader && (int) $existingLeader->user_id === $userId) {
                return $existingLeader;
            }

            // Kalau ada leader LAIN, tolak
            if ($existingLeader) {
                throw new \RuntimeException(
                    "Sudah ada leader aktif di organisasi ini (user_id={$existingLeader->user_id}). "
                    . "Gunakan transferLeadership() untuk mengganti."
                );
            }

            // Update OR create — paksa active
            $membership = OrganizationMembership::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'user_id'         => $userId,
                ],
                [
                    'access_role'       => 'leader',
                    'membership_status' => 'active',
                    'position_label'    => $positionLabel ?? 'Ketua',
                    'joined_at'         => now(),
                    'left_at'           => null,
                ]
            );

            Log::info('[OrgLeadership] promote: sukses.', [
                'org_id'  => $organizationId,
                'user_id' => $userId,
                'pos'     => $positionLabel,
            ]);

            return $membership->fresh();
        });
    }

    /**
     * Pindahkan jabatan leader dari $fromUserId ke $toUserId.
     *
     * ⚠️ PERUBAHAN KRITIS dari versi lama:
     *    Gunakan updateOrCreate untuk $toUserId agar row dibuat jika belum ada.
     *    Versi lama pakai update() → 0 rows affected jika target belum punya membership.
     */
    public static function transferLeadership(
        int $organizationId,
        int $fromUserId,
        int $toUserId,
        ?string $positionLabel = null
    ): void {
        if ($fromUserId === $toUserId) {
            throw new \InvalidArgumentException('User asal dan tujuan transfer tidak boleh sama.');
        }

        DB::transaction(function () use ($organizationId, $fromUserId, $toUserId, $positionLabel) {

            // 1. Lock leader saat ini
            $currentLeader = OrganizationMembership::where('organization_id', $organizationId)
                ->where('access_role', 'leader')
                ->where('membership_status', 'active')
                ->lockForUpdate()
                ->first();

            if (!$currentLeader) {
                throw new \RuntimeException(
                    "Organisasi ini belum punya leader. Gunakan promoteToLeader() dulu."
                );
            }

            if ((int) $currentLeader->user_id !== $fromUserId) {
                throw new \RuntimeException(
                    "User ID {$fromUserId} bukan leader aktif organisasi ini "
                    . "(leader sekarang: {$currentLeader->user_id})."
                );
            }

            // 2. Demote current leader → officer
            $currentLeader->update([
                'access_role'    => 'officer',
                'position_label' => $currentLeader->position_label
                    ? $currentLeader->position_label . ' (mantan)'
                    : 'Mantan Ketua',
            ]);

            // 3. Promote target → leader (UPDATE OR CREATE — inti fix!)
            OrganizationMembership::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'user_id'         => $toUserId,
                ],
                [
                    'access_role'       => 'leader',
                    'membership_status' => 'active',
                    'position_label'    => $positionLabel ?? 'Ketua',
                    'joined_at'         => now(),
                    'left_at'           => null,
                ]
            );

            Log::info('[OrgLeadership] transfer: sukses.', [
                'org_id'    => $organizationId,
                'from_user' => $fromUserId,
                'to_user'   => $toUserId,
            ]);
        });
    }

    /**
     * Demote leader tanpa transfer.
     */
    public static function demoteLeader(int $organizationId, int $userId, string $newRole = 'officer'): void
    {
        $validRoles = ['officer', 'pj', 'member'];
        if (!in_array($newRole, $validRoles, true)) {
            throw new \InvalidArgumentException("newRole harus salah satu dari: " . implode(', ', $validRoles));
        }

        DB::transaction(function () use ($organizationId, $userId, $newRole) {
            $membership = OrganizationMembership::where('organization_id', $organizationId)
                ->where('user_id', $userId)
                ->where('access_role', 'leader')
                ->lockForUpdate()
                ->first();

            if (!$membership) {
                throw new \RuntimeException("User bukan leader aktif di organisasi ini.");
            }

            $membership->update(['access_role' => $newRole]);
        });
    }

    public static function currentLeader(int $organizationId): ?OrganizationMembership
    {
        return OrganizationMembership::where('organization_id', $organizationId)
            ->where('access_role', 'leader')
            ->where('membership_status', 'active')
            ->first();
    }

    public static function hasLeader(int $organizationId): bool
    {
        return self::currentLeader($organizationId) !== null;
    }
}