<?php

namespace App\Http\Controllers;

use App\Models\UserQrCode;
use Illuminate\Http\Request;

class QrValidationController extends Controller
{
    /**
     * Tampilkan profil publik berdasarkan credential UUID
     */
    public function show($uuid)
    {
        // 1. Cari credential berdasarkan UUID
        $credential = UserQrCode::with('user')->where('qr_uuid', $uuid)->first();

        // 2. Jika tidak ditemukan atau user dihapus
        if (!$credential || !$credential->user) {
            abort(404, 'QR Code tidak dikenali.');
        }

        $user = $credential->user;

        // 3. Validasi status QR
        if (!$credential->is_active || $credential->is_revoked) {
            abort(403, 'QR Code ini sudah tidak aktif atau dicabut.');
        }

        // 4. Validasi status user
        if (!$user->is_active) {
            abort(403, 'Akun pengguna tidak aktif.');
        }

        // 5. Catat event scan (anti-replay, analitik)
        $credential->increment('scan_count');
        $credential->last_used_at = now();
        $credential->last_scan_ip = request()->ip();
        $credential->last_scan_device = request()->userAgent();
        $credential->save();

        // 6. Tampilkan halaman profil publik
        return view('qr.public-profile', compact('user', 'credential'));
    }
}