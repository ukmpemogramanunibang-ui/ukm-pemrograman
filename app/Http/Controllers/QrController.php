<?php

namespace App\Http\Controllers;

use App\Models\UserQrCode;
use App\Services\QrValidationService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QrController extends Controller
{
    protected $qrValidationService;

    public function __construct(QrValidationService $qrValidationService)
    {
        $this->qrValidationService = $qrValidationService;
    }

    /**
     * Validasi QR credential berdasarkan UUID (public route).
     */
    public function validate($uuid)
    {
        $credential = UserQrCode::where('qr_uuid', $uuid)->first();

        if (!$credential) {
            return view('qr.invalid', ['message' => 'QR Code tidak dikenal.']);
        }

        $result = $this->qrValidationService->validateCredential($credential);

        if (!$result['valid']) {
            return view('qr.invalid', ['message' => $result['message']]);
        }

        return view('qr.validation', [
            'user'       => $result['user'],
            'credential' => $credential,
            'status'     => $result['status'],
            'profile'    => $result['profile'] ?? [],
        ]);
    }

    /**
     * Halaman form download QR (menampilkan daftar user).
     */
    public function index()
    {
        $users = \App\Models\User::all();
        return view('owner.qr.download', compact('users'));
    }
   /**
 * Rotate QR manual (tanpa download).
 * Revoke QR aktif lama → buat QR baru → redirect ke detail.
 */
public function rotate($user_id, Request $request, QrCodeService $qrCodeService)
{
    $user = \App\Models\User::findOrFail($user_id);

    $qrCodeService->rotateForUser($user, [
        'reason'     => 'rotated_manual',
        'revoked_by' => auth()->id(),
    ]);

    return redirect()
        ->route('owner.management.qr.detail', $user->id)
        ->with('success', '✅ QR berhasil di-rotate. QR lama tidak berlaku, QR baru sudah aktif.');
}

/**
 * History semua QR user (termasuk yang sudah dicabut).
 */
public function history($user_id)
{
    $user = \App\Models\User::findOrFail($user_id);

    $history = UserQrCode::where('user_id', $user_id)
        ->withTrashed()
        ->orderByDesc('id')
        ->get();

    return view('owner.management.qr.history', compact('user', 'history'));
}
public function download($qr_id, $format, Request $request, QrCodeService $qrCodeService)
{
    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'pdf'];
    $format  = strtolower($format);

    $wantsJson = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

    // Helper untuk balas error
    $error = function ($msg, $code = 400) use ($wantsJson) {
        if ($wantsJson) {
            return response()->json(['error' => $msg, 'code' => $code], $code);
        }
        return redirect()->back()->with('error', $msg);
    };

    if (!in_array($format, $allowed)) {
        return $error('Format tidak didukung.', 400);
    }

    $qr = UserQrCode::find($qr_id);

    if (!$qr) {
        return $error('QR tidak ditemukan.', 404);
    }

    if ($qr->is_revoked || !$qr->is_active) {
        return $error(
            'QR berstatus ' . ($qr->is_revoked ? 'DICABUT' : 'TIDAK AKTIF') . '. Hanya QR Aktif yang bisa diunduh.',
            403
        );
    }

    // PDF: handler khusus
    if ($format === 'pdf') {
        try {
            return $this->handlePdfDownload($qr, $qrCodeService);
        } catch (\Throwable $e) {
            Log::error('PDF download failed', ['qr_id' => $qr_id, 'error' => $e->getMessage()]);
            return $error('Gagal generate PDF: ' . $e->getMessage(), 500);
        }
    }

    // Image
    try {
        $response = $qrCodeService->downloadResponse($qr, $format);

        if (!$response) {
            return $error('Gagal membuat file QR. Cek log server.', 500);
        }

        return $response;
    } catch (\Throwable $e) {
        Log::error('QR download failed', [
            'qr_id'  => $qr_id,
            'format' => $format,
            'error'  => $e->getMessage(),
            'trace'  => $e->getTraceAsString(),
        ]);
        return $error('Error: ' . $e->getMessage(), 500);
    }
}
    /**
     * Hapus semua QR yang sudah dicabut (revoked) secara permanen.
     */
    public function cleanupRevoked()
    {
        $revokedQrs = UserQrCode::where('is_revoked', true)
            ->orWhere('is_active', false)
            ->get();

        $count = 0;
        foreach ($revokedQrs as $qr) {
            if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
                Storage::disk('public')->delete($qr->qr_image);
            }
            $qr->forceDelete();
            $count++;
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$count} QR yang sudah dicabut.");
    }

    /**
     * Hapus QR yang dipilih (checkbox).
     */
    public function deleteSelected(Request $request)
    {
        $ids = json_decode($request->input('ids', '[]'), true);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada QR yang dipilih.');
        }

        $count = 0;
        foreach ($ids as $id) {
            $qr = UserQrCode::find($id);
            if ($qr) {
                if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
                    Storage::disk('public')->delete($qr->qr_image);
                }
                $qr->forceDelete();
                $count++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$count} QR.");
    }

    /**
     * Handler khusus untuk download PDF 2 halaman.
     */
    private function handlePdfDownload(UserQrCode $qr, QrCodeService $qrCodeService)
    {
        $user = $qr->user;
        $qrImageData = null;

        // Coba ambil dari file yang sudah ada
        if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
            $qrImageData = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($qr->qr_image));
        } else {
            // Generate ulang jika belum ada
            $path = $qrCodeService->generateAndSaveQrImage($qr, 'png');
            if ($path && Storage::disk('public')->exists($path)) {
                $qrImageData = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($path));
            }
        }

        if (!$qrImageData) {
            return redirect()->back()->with('error', 'Gagal menghasilkan data QR untuk PDF.');
        }

        $data = [
            'user'          => $user,
            'qr'            => $qr,
            'qrImageData'   => $qrImageData,
            'role'          => $user->level->name ?? 'Anggota',
            'generated_at'  => now()->format('d F Y H:i'),
        ];

        $filename = 'UNICODE_QR_' . ($user->nim ?? $user->id) . '.pdf';

        try {
            $pdf = Pdf::loadView('owner.qr.pdf-template', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => true,
                ]);

            return $pdf->download($filename);
        } catch (Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal generate PDF.');
        }
    }
}