<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;
use App\Services\OrganizationContext;

class AttendanceSessionPolicy
{
    /**
     * Cek akses baca berdasarkan keanggotaan di organisasi terkait sesi.
     */
    public function view(User $user, AttendanceSession $session): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;

        return $user->organizationMemberships()
            ->where('organization_id', $session->organization_id)
            ->where('membership_status', 'active')
            ->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    /**
     * Hanya Ketua/Pengurus di organisasi tersebut yang bisa membuat sesi baru.
     */
    public function create(User $user): bool
    {
        $orgId = OrganizationContext::id();
        if (!$orgId) return false;

        return OrganizationContext::authorize($user, $orgId, 'officer');
    }

    /**
     * Update sesi: Hanya Leader/Officer di org yang sama, dan status belum closed.
     */
    public function update(User $user, AttendanceSession $session): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;

        if (!in_array($session->session_status, ['draft', 'scheduled', 'open', 'paused'])) return false;

        return OrganizationContext::authorize($user, $session->organization_id, 'officer');
    }

    /**
     * Hapus sesi: Hanya Owner/Superadmin, atau Ketua di org tersebut.
     */
    public function delete(User $user, AttendanceSession $session): bool
    {
        if (in_array((int) $user->level_id, [1, 2])) return true;
        return OrganizationContext::authorize($user, $session->organization_id, 'leader');
    }

    /**
     * Lifecycle: Buka Sesi
     */
    public function open(User $user, AttendanceSession $session): bool
    {
        if (!in_array($session->session_status, ['scheduled', 'paused'])) return false;
        if (in_array((int) $user->level_id, [1, 2])) return true;
        return OrganizationContext::authorize($user, $session->organization_id, 'officer');
    }

    /**
     * Lifecycle: Tutup Sesi
     */
    public function close(User $user, AttendanceSession $session): bool
    {
        if (!in_array($session->session_status, ['open', 'extended', 'paused'])) return false;
        if (in_array((int) $user->level_id, [1, 2])) return true;
        return OrganizationContext::authorize($user, $session->organization_id, 'officer');
    }

    /**
     * Lifecycle: Perpanjang Sesi
     */
    public function extend(User $user, AttendanceSession $session): bool
    {
        if (!in_array($session->session_status, ['open', 'extended'])) return false;
        if (in_array((int) $user->level_id, [1, 2])) return true;
        return OrganizationContext::authorize($user, $session->organization_id, 'officer');
    }

    /**
     * Lifecycle: Batalkan Sesi (Hanya Ketua/Owner)
     */
    public function cancel(User $user, AttendanceSession $session): bool
    {
        if (in_array($session->session_status, ['closed', 'reconciled', 'archived'])) return false;
        if (in_array((int) $user->level_id, [1, 2])) return true;
        return OrganizationContext::authorize($user, $session->organization_id, 'leader');
    }

    /**
     * Manual entry (koreksi kehadiran oleh pengurus)
     */
    public function manualEntry(User $user, AttendanceSession $session): bool
    {
        if (!in_array($session->session_status, ['open', 'extended'])) return false;
        if (in_array((int) $user->level_id, [1, 2])) return true;
        return OrganizationContext::authorize($user, $session->organization_id, 'officer');
    }
}