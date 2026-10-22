<?php

namespace App\Services;

use App\Models\UserQrCode;
use Carbon\Carbon;

class QrValidationService
{
    public function validateCredential(UserQrCode $credential): array
    {
        // 1. Cek aktif & belum dicabut
        if (!$credential->is_active || $credential->is_revoked) {
            return [
                'valid'   => false,
                'message' => 'QR Code ini tidak aktif atau telah dicabut.',
            ];
        }

        // 2. Cek expired
        if ($credential->expired_at && Carbon::now()->greaterThan($credential->expired_at)) {
            return [
                'valid'   => false,
                'message' => 'QR Code ini sudah kedaluwarsa.',
            ];
        }

        // 3. Ambil user
        $user = $credential->user;
        if (!$user) {
            return [
                'valid'   => false,
                'message' => 'User tidak ditemukan.',
            ];
        }

        // 4. Cek status user
        if ($user->trashed()) {
            return [
                'valid'   => false,
                'message' => 'Akun user telah dihapus.',
            ];
        }

        if (!$user->is_active) {
            return [
                'valid'   => false,
                'message' => 'Akun user tidak aktif.',
            ];
        }

        if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
            return [
                'valid'   => false,
                'message' => 'Akun user sedang dikunci.',
            ];
        }

        // 5. Update statistik scan
        $credential->increment('scan_count');
        $credential->last_used_at = now();
        $credential->last_scan_ip = request()->ip();
        $credential->last_scan_device = request()->userAgent();
        $credential->save();

        // 6. Kembalikan data
        return [
            'valid'   => true,
            'message' => 'QR Code valid.',
            'user'    => $user,
            'status'  => 'active',
            'profile' => [
                'name'          => $user->name,
                'nim'           => $user->nim,
                'email'         => $user->email,
                'member_number' => $user->member_number,
                'role'          => $user->level->name ?? 'Guest',
                'avatar'        => $user->avatar_url,
                'jabatans'      => $user->jabatans->where('pivot.is_primary', true)->pluck('name'),
                'total_points'  => $user->total_points,
                'rank'          => $user->rank,
                'is_active'     => $user->is_active,
            ],
        ];
    }
}