<?php

namespace App\Http\Controllers\Owner\Management;

use App\Http\Controllers\Controller;
use App\Models\UserQrCode;
use App\Services\QrCodeService; // Import Service Anda
use Illuminate\Http\Request;

class QrController extends Controller
{
    protected $qrCodeService;

    // Dependency Injection: Service akan di-inject secara otomatis oleh Laravel
    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    // ... method lain yang sudah ada (index, generate, dll) ...

    /**
     * Download QR Code berdasarkan User ID dan Format.
     */
    public function download(Request $request, $userId)
    {
        // Validasi format input (Recommended)
        $request->validate([
            'format' => 'in:png,jpg,jpeg,webp,svg'
        ]);

        // Cari QR aktif milik user tersebut
        $qr = UserQrCode::where('user_id', $userId)
            ->where('is_active', true)
            ->latest() // Prioritaskan yang terbaru
            ->first();

        if (!$qr) {
            abort(404, 'QR Code aktif tidak ditemukan untuk user ini.');
        }

        // Gunakan Service yang sudah di-inject untuk menghasilkan response download
        // Fungsi downloadResponse ini otomatis menangani file corrupt dan regenerasi!
        $format = $request->get('format', 'png');
        $response = $this->qrCodeService->downloadResponse($qr, $format);

        if (!$response) {
            abort(500, 'Gagal generate atau mendownload QR Code.');
        }

        return $response;
    }
}