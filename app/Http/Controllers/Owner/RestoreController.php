<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\DataProtectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ✅ Pastikan ini ada
use Illuminate\Support\Facades\Log;     // ✅ Untuk logging error

class RestoreController extends Controller
{
    protected $service;

    public function __construct(DataProtectionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            // Pastikan folder backups ada
            if (!Storage::exists('backups')) {
                Storage::makeDirectory('backups');
            }

            $files = collect(Storage::files('backups'))
                ->map(fn($f) => basename($f))
                ->filter(fn($f) => !empty($f)) // filter file kosong
                ->sortDesc()
                ->values()
                ->toArray();

            $lastBackup = $files[0] ?? null;

            return view('owner.restore.index', compact('files', 'lastBackup'));
        } catch (\Exception $e) {
            Log::error('Restore page error: ' . $e->getMessage());
            // Tampilkan error di view jika debug true
            if (config('app.debug')) {
                throw $e;
            }
            // Fallback: tampilkan halaman dengan pesan error
            return view('owner.restore.index', [
                'files' => [],
                'lastBackup' => null,
                'error' => 'Gagal memuat data backup: ' . $e->getMessage()
            ]);
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'file' => 'nullable|string',
        ]);

        try {
            if ($request->file) {
                $count = $this->service->restoreFromFile($request->file);
                $message = "✅ Berhasil memulihkan $count data dari file {$request->file}.";
            } else {
                $count = $this->service->restoreAllFromProtected();
                $message = "✅ Berhasil memulihkan $count data dari protected database.";
            }
            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }

    public function createBackup()
    {
        try {
            $count = $this->service->syncAllToProtected();
            $path = $this->service->createFileBackup();
            return back()->with('success', "✅ Backup berhasil! $count data disimpan di protected database dan file $path.");
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal backup: ' . $e->getMessage());
        }
    }

    public function listFiles()
    {
        try {
            $files = Storage::files('backups');
            return response()->json($files);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function download($filename)
{
    // Gunakan pengecekan level name, bukan role
    $user = auth()->user();
    if (!$user || $user->level->name !== 'owner') {
        abort(403, 'Hanya Owner yang dapat mengunduh backup.');
    }

    $path = 'backups/' . $filename;

    if (!Storage::exists($path)) {
        abort(404, 'File backup tidak ditemukan.');
    }

    return Storage::download($path, $filename, [
        'Content-Type' => 'application/json',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
}