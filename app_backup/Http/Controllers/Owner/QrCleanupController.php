<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class QrCleanupController extends Controller
{
    // PIN khusus yang diwajibkan untuk menghapus
    const CLEANUP_PIN = '1234567890';

    /**
     * Tampilkan halaman cleanup QR.
     */
    public function index()
    {
        // Pastikan hanya Owner yang bisa akses
        if (auth()->user()->level_id != 1) {
            abort(403, 'Hanya Owner yang dapat mengakses halaman ini.');
        }

        // Scan semua file PNG di storage/public/qr
        $files = Storage::disk('public')->files('qr');
        $corruptedFiles = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'png') {
                continue;
            }

            $fullPath = Storage::disk('public')->path($file);
            $content = file_get_contents($fullPath);
            
            // Deteksi korupsi: jika gagal diubah menjadi gambar oleh GD
            $img = @imagecreatefromstring($content);
            if ($img === false) {
                $corruptedFiles[] = $file;
            } else {
                imagedestroy($img);
            }
        }

        return view('owner.qr.cleanup', compact('corruptedFiles'));
    }

    /**
     * Proses penghapusan file QR yang korup.
     */
    public function destroy(Request $request)
    {
        // Validasi user hanya Owner
        if (auth()->user()->level_id != 1) {
            return response()->json(['error' => 'Hanya Owner yang bisa melakukan ini.'], 403);
        }

        $request->validate([
            'pin' => 'required|string',
            'reason' => 'required|string|min:5',
            'files' => 'required|array',
            'files.*' => 'string',
        ]);

        // Verifikasi PIN khusus
        if ($request->pin !== self::CLEANUP_PIN) {
            return response()->json(['error' => 'PIN salah. Penghapusan dibatalkan.'], 403);
        }

        // Verifikasi ulang bahwa file yang dipilih benar-benar korup (untuk keamanan ekstra)
        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($request->files as $file) {
            $fullPath = Storage::disk('public')->path($file);
            if (!file_exists($fullPath)) {
                $skippedCount++;
                continue;
            }

            // Cek kembali korupsi sebelum menghapus (lapis keamanan kedua)
            $img = @imagecreatefromstring(file_get_contents($fullPath));
            if ($img === false) {
                // File benar-benar korup -> hapus
                Storage::disk('public')->delete($file);
                $deletedCount++;
            } else {
                imagedestroy($img);
                $skippedCount++;
            }
        }

        // Catat log audit penghapusan
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'qr_cleanup',
            'description' => "Menghapus $deletedCount file QR korup. Alasan: " . $request->reason,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => "Berhasil menghapus $deletedCount file QR yang korup. ($skippedCount file dilewati karena tidak korup atau tidak ada)."
        ]);
    }
}