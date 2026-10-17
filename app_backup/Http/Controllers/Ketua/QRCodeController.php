<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserQrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\PdfWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class QRCodeController extends Controller
{
    /**
     * Statistik QR Code.
     * GET /ketua/manajemen-anggota/qr/api/statistik
     */
    public function statistik()
    {
        $totalAnggota = User::where('ukm_id', 1)->count();

        $sudahQr = UserQrCode::where('status', 'active')->count();

        $belumQr = User::where('ukm_id', 1)
            ->where('is_active', true)
            ->whereDoesntHave('qrCode')
            ->count();

        $expired = UserQrCode::where('status', 'expired')->count();

        $nonaktif = User::where('ukm_id', 1)
            ->where('is_active', false)
            ->count();

        return response()->json([
            'total_anggota' => $totalAnggota,
            'sudah_qr'      => $sudahQr,
            'belum_qr'      => $belumQr,
            'expired'       => $expired,
            'nonaktif'      => $nonaktif,
        ]);
    }

    /**
     * Daftar anggota (search & filter).
     * GET /ketua/manajemen-anggota/qr/api/anggota?search=&filter=
     */
    public function anggota(Request $request)
    {
        $query = User::with(['level', 'jabatans', 'qrCode'])
                     ->where('ukm_id', 1);

        $filter = $request->input('filter');
        if ($filter && $filter !== 'semua') {
            switch ($filter) {
                case 'ketua':
                    $query->whereHas('jabatans', fn($q) => $q->where('name', 'Ketua'));
                    break;
                case 'pengurus':
                    $query->whereHas('jabatans', fn($q) => $q->whereIn('name', ['Bendahara','Sekretaris','Koordinator']));
                    break;
                case 'anggota':
                    $query->whereHas('level', fn($q) => $q->where('name', 'anggota'));
                    break;
                case 'alumni':
                    $query->whereHas('level', fn($q) => $q->where('name', 'alumni'));
                    break;
                case 'aktif':
                    $query->where('is_active', true);
                    break;
                case 'tidakaktif':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('user_uuid', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $members = $query->get()->map(function ($user) {
            $qr = $user->qrCode;
            return [
                'id'        => $user->id,
                'user_uuid' => $user->user_uuid,
                'name'      => $user->name,
                'nim'       => $user->nim,
                'email'     => $user->email,
                'jabatan'   => $user->jabatans->pluck('name')->first() ?? '',
                'level'     => $user->level->name ?? '',
                'is_active' => $user->is_active,
                'has_qr'    => $qr ? true : false,
                'qr_status' => $qr ? $qr->status : 'none',
            ];
        });

        return response()->json(['members' => $members]);
    }

    /**
     * Generate QR Code untuk user yang dipilih.
     * POST /ketua/manajemen-anggota/qr/api/generate
     */
    public function generateQR(Request $request)
    {
        $request->validate([
            'user_ids'   => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $results = [];

        foreach ($request->user_ids as $userId) {
            $user = User::findOrFail($userId);

            // Nonaktifkan QR lama
            if ($qr = $user->qrCode) {
                $qr->update(['status' => 'revoked']);
            }

            $token = Str::random(120);
            $qr = UserQrCode::create([
                'user_id'      => $user->id,
                'qr_uuid'      => (string) Str::uuid(),
                'token'        => $token,
                'generated_at' => now(),
                'status'       => 'active',
            ]);

            $results[] = [
                'user_id'   => $user->id,
                'name'      => $user->name,
                'token'     => $token,
                'qr_status' => 'active',
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * Simpan QR (konfirmasi).
     * POST /ketua/manajemen-anggota/qr/api/save
     */
    public function saveQR(Request $request)
    {
        // Data sudah tersimpan di generate, bisa ditambahkan log aktivitas
        return response()->json([
            'success' => true,
            'message' => 'QR Code sudah aktif dan tersimpan.',
        ]);
    }

    /**
     * Print QR Code dalam berbagai format.
     * POST /ketua/manajemen-anggota/qr/api/print
     */
    public function printQR(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $userIds = $request->input('user_ids', []);

        // Ambil data QR terbaru untuk user yang dipilih
        $qrCodes = UserQrCode::whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->get();

        if ($qrCodes->isEmpty()) {
            return response()->json(['error' => 'Tidak ada QR aktif yang bisa dicetak.'], 404);
        }

        // Siapkan konten file sesuai format
        switch ($format) {
            case 'png':
                return $this->printPng($qrCodes);
            case 'pdf':
            case 'idcard':
                return $this->printPdf($qrCodes, $format === 'idcard');
            case 'zip':
                return $this->printZip($qrCodes);
            default:
                return response()->json(['error' => 'Format tidak didukung'], 400);
        }
    }

    /**
     * Download QR sebagai ZIP.
     * POST /ketua/manajemen-anggota/qr/api/download
     */
    public function downloadQR(Request $request)
    {
        $userIds = $request->input('user_ids', []);

        $qrCodes = UserQrCode::whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->get();

        if ($qrCodes->isEmpty()) {
            return response()->json(['error' => 'Tidak ada QR untuk diunduh.'], 404);
        }

        return $this->printZip($qrCodes);
    }

    // ======================= METODE PEMBANTU =======================

    private function generateQrImage(string $data): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->build();

        return $result->getString();
    }

    private function printPng($qrCodes)
    {
        // Jika hanya satu QR, langsung download PNG
        if ($qrCodes->count() === 1) {
            $qr = $qrCodes->first();
            $loginUrl = config('app.url') . '/login/qr/' . $qr->token;
            $image = $this->generateQrImage($loginUrl);

            return Response::make($image, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="qr_' . $qr->user_id . '.png"',
            ]);
        }

        // Jika banyak, masukkan ke ZIP
        return $this->printZip($qrCodes);
    }

    private function printPdf($qrCodes, $isIdCard = false)
    {
        // Untuk sederhana, kita kirim sebagai ZIP berisi PNG.
        // Anda bisa kembangkan dengan DomPDF untuk PDF utuh.
        return $this->printZip($qrCodes);
    }

    private function printZip($qrCodes)
    {
        $zip = new ZipArchive();
        $zipName = 'qr_codes_' . now()->format('Ymd_His') . '.zip';
        $tempPath = storage_path('app/temp/' . $zipName);

        if (!is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        if ($zip->open($tempPath, ZipArchive::CREATE) !== true) {
            return response()->json(['error' => 'Tidak dapat membuat ZIP'], 500);
        }

        foreach ($qrCodes as $qr) {
            $loginUrl = config('app.url') . '/login/qr/' . $qr->token;
            $image = $this->generateQrImage($loginUrl);
            $filename = 'qr_' . $qr->user->name . '_' . $qr->user->nim . '.png';
            $zip->addFromString($filename, $image);
        }

        $zip->close();

        return response()->download($tempPath, $zipName)->deleteFileAfterSend(true);
    }
}