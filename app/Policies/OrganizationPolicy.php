<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationContext;

/**
 * OrganizationPolicy
 *
 * Aturan otorisasi untuk model Organization.
 *
 * Prinsip:
 *   - Owner (level 1) & Superadmin (level 2) → akses global semua organisasi
 *   - Leader (Ketua) → bisa manage organisasi yang dipimpinnya
 *   - Officer (Pengurus) → bisa manage operasional (keuangan, anggota, dll)
 *   - Member (Anggota) → hanya bisa lihat
 *
 * Catatan penting:
 *   - Hak akses TIDAK ditentukan oleh `level_id` untuk operasi per-organisasi.
 *     `level_id` hanya dipakai untuk cek "apakah user ini platform admin?".
 *     Sisanya ditentukan oleh `OrganizationContext::authorize()` yang membaca
 *     keanggotaan user di organisasi tersebut.
 */
class OrganizationPolicy
{
    /**
     * Level akun platform yang punya akses global.
     *   1 = Owner
     *   2 = Superadmin
     */
    private const PLATFORM_ADMIN_LEVELS = [1, 2];

    /* ═══════════════════════════════════════════════════════
     | VIEW — akses baca
     * ═══════════════════════════════════════════════════════ */

    /**
     * Boleh melihat daftar organisasi?
     *
     * Semua user terautentikasi boleh membuka halaman daftar.
     * Controller bertanggung jawab memfilter hanya organisasi
     * yang diikuti user (via `activeOrganizations()`).
     */
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    /**
     * Boleh melihat detail organisasi tertentu?
     *
     * - Owner/Superadmin: ya, semua organisasi
     * - User lain: hanya jika punya membership aktif
     */
    public function view(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return $this->hasActiveMembership($user, $organization);
    }

    /* ═══════════════════════════════════════════════════════
     | MANAGE — akses operasional per modul
     * ═══════════════════════════════════════════════════════ */

    /**
     * Boleh mengelola organisasi secara umum
     * (edit profil, ubah settings, aktif/nonaktifkan modul)?
     *
     * - Owner/Superadmin: ya
     * - Leader (Ketua) di organisasi tsb: ya
     * - Officer: tidak (kecuali diberi izin khusus via `permissions` JSON)
     */
    public function manage(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return OrganizationContext::authorize($user, $organization->id, 'leader');
    }

    /**
     * Boleh mengelola keuangan organisasi
     * (tagihan, pembayaran, laporan)?
     *
     * - Owner/Superadmin: ya
     * - Leader & Officer: ya (bendahara biasanya officer/leader)
     */
    public function manageFinance(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return OrganizationContext::authorize($user, $organization->id, 'officer');
    }

    /**
     * Boleh mengelola anggota organisasi
     * (tambah, ubah jabatan, keluarkan)?
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return OrganizationContext::authorize($user, $organization->id, 'officer');
    }

    /**
     * Boleh mengelola absensi organisasi
     * (buat sesi, buka/tutup, koreksi manual)?
     */
    public function manageAttendance(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return OrganizationContext::authorize($user, $organization->id, 'officer');
    }

    /**
     * Boleh mengelola LMS organisasi
     * (buat grup, tugas, materi, review submission)?
     */
    public function manageLms(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return OrganizationContext::authorize($user, $organization->id, 'officer');
    }

    /**
     * Boleh mengelola Game Center organisasi
     * (buat game, kompetisi, challenge)?
     */
    public function manageGames(User $user, Organization $organization): bool
    {
        if ($this->isPlatformAdmin($user)) {
            return true;
        }

        return OrganizationContext::authorize($user, $organization->id, 'officer');
    }

    /* ═══════════════════════════════════════════════════════
     | CRUD — buat/ubah/hapus organisasi
     * ═══════════════════════════════════════════════════════ */

    /**
     * Boleh membuat organisasi baru?
     *
     * HANYA Owner (level 1). Superadmin tidak boleh menambah
     * organisasi baru — hanya mengelola yang sudah ada.
     */
    public function create(User $user): bool
    {
        return (int) $user->level_id === 1;
    }

    /**
     * Boleh mengubah data organisasi? (sinonim `manage`)
     */
    public function update(User $user, Organization $organization): bool
    {
        return $this->manage($user, $organization);
    }

    /**
     * Boleh menghapus organisasi (soft delete)?
     * Hanya Owner.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return (int) $user->level_id === 1;
    }

    /**
     * Boleh restore organisasi yang sudah soft-deleted?
     * Hanya Owner.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return (int) $user->level_id === 1;
    }

    /**
     * Boleh menghapus permanen (force delete)?
     * Hanya Owner. Sebaiknya dihindari di production.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return (int) $user->level_id === 1;
    }

    /* ═══════════════════════════════════════════════════════
     | HELPERS
     * ═══════════════════════════════════════════════════════ */

    /**
     * Cek apakah user adalah platform admin (Owner/Superadmin).
     * Platform admin bypass semua cek per-organisasi.
     */
    private function isPlatformAdmin(User $user): bool
    {
        return in_array((int) $user->level_id, self::PLATFORM_ADMIN_LEVELS, true);
    }

    /**
     * Cek apakah user punya keanggotaan aktif di organisasi tertentu.
     */
    private function hasActiveMembership(User $user, Organization $organization): bool
    {
        return $user->organizationMemberships()
            ->where('organization_id', $organization->id)
            ->where('membership_status', 'active')
            ->exists();
    }
}