<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;

class AttendanceSessionPolicy
{
    /**
     * Level roles yang diizinkan (bisa disesuaikan dengan sistem Anda).
     */
    protected function role(User $user): string
    {
        return strtolower($user->level?->slug ?? $user->level?->name ?? 'guest');
    }

    protected function isOwner(User $u): bool      { return in_array($this->role($u), ['owner']); }
    protected function isSuperadmin(User $u): bool { return in_array($this->role($u), ['superadmin']); }
    protected function isKetua(User $u): bool      { return in_array($this->role($u), ['ketua']); }
    protected function isPengurus(User $u): bool   { return in_array($this->role($u), ['pengurus']); }

    /* ============================================================
     * BASIC CRUD
     * ============================================================ */

    public function viewAny(User $u): bool
    {
        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u) || $this->isPengurus($u);
    }

    public function view(User $u, AttendanceSession $s): bool
    {
        return $this->viewAny($u);
    }

    public function create(User $u): bool
    {
        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u);
    }

    public function update(User $u, AttendanceSession $s): bool
    {
        if ($this->isOwner($u) || $this->isSuperadmin($u)) return true;

        // Ketua hanya boleh update sesi yang belum open
        if ($this->isKetua($u)) {
            return in_array($s->session_status, ['draft', 'scheduled']);
        }

        return false;
    }

    public function delete(User $u, AttendanceSession $s): bool
    {
        return $this->isOwner($u) || $this->isSuperadmin($u);
    }

    /* ============================================================
     * LIFECYCLE
     * ============================================================ */

    public function open(User $u, AttendanceSession $s): bool
    {
        // Hanya boleh open jika status scheduled
        if (!in_array($s->session_status, ['scheduled', 'paused'])) return false;

        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u);
    }

    public function close(User $u, AttendanceSession $s): bool
    {
        if (!in_array($s->session_status, ['open', 'extended', 'paused'])) return false;

        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u);
    }

    public function cancel(User $u, AttendanceSession $s): bool
    {
        // Tidak boleh cancel yang sudah closed
        if (in_array($s->session_status, ['closed', 'reconciled', 'archived'])) return false;

        return $this->isOwner($u) || $this->isSuperadmin($u);
    }

    public function extend(User $u, AttendanceSession $s): bool
    {
        if (!in_array($s->session_status, ['open', 'extended'])) return false;

        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u);
    }

    public function pause(User $u, AttendanceSession $s): bool
    {
        if ($s->session_status !== 'open') return false;

        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u) || $this->isPengurus($u);
    }

    public function reconcile(User $u, AttendanceSession $s): bool
    {
        return $this->isOwner($u) || $this->isSuperadmin($u);
    }

    public function archive(User $u, AttendanceSession $s): bool
    {
        return $this->isOwner($u);
    }

    /* ============================================================
     * ATTENDANCE OPERATIONS
     * ============================================================ */

    public function manualEntry(User $u, AttendanceSession $s): bool
    {
        // Manual entry hanya saat window aktif
        if (!in_array($s->session_status, ['open', 'extended'])) return false;

        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u) || $this->isPengurus($u);
    }

    public function monitor(User $u, AttendanceSession $s): bool
    {
        return $this->viewAny($u);
    }

    public function audit(User $u): bool
    {
        return $this->isOwner($u) || $this->isSuperadmin($u) || $this->isKetua($u);
    }
}