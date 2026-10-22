<?php

namespace App\Console\Commands;

use App\Services\DataProtectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // ✅ Tambahkan ini

class RestoreDataCommand extends Command
{
    protected $signature = 'restore:data {--file= : Nama file backup (opsional)} {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Pulihkan data dari protected database atau file backup';

    public function handle(DataProtectionService $service): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  Restore akan menimpa data saat ini. Lanjutkan?')) {
                $this->info('Restore dibatalkan.');
                return 0;
            }
        }

        try {
            if ($this->option('file')) {
                $filename = $this->option('file');
                $this->info("📂 Memulihkan dari file: $filename");
                $count = $service->restoreFromFile($filename);
                $this->info("✅ $count data dipulihkan dari file $filename.");
            } else {
                $this->info("📂 Memulihkan dari protected database...");
                $count = $service->restoreAllFromProtected();
                $this->info("✅ $count data dipulihkan dari protected database.");
            }
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Gagal restore: ' . $e->getMessage());
            Log::error('RestoreDataCommand failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }
    }
}