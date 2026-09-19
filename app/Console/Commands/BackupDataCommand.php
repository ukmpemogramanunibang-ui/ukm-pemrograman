<?php

namespace App\Console\Commands;

use App\Services\DataProtectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // ✅ Tambahkan ini

class BackupDataCommand extends Command
{
    protected $signature = 'backup:data {--file : Simpan juga ke file JSON} {--name= : Nama backup (opsional)}';
    protected $description = 'Backup data penting ke protected database dan/atau file';

    public function handle(DataProtectionService $service): int
    {
        $this->info('🔄 Memulai backup data...');

        try {
            $name = $this->option('name') ?? 'backup_' . now()->format('Ymd_His');
            $count = $service->syncAllToProtected($name);
            $this->info("✅ $count data berhasil disinkronkan ke protected database.");

            if ($this->option('file')) {
                $path = $service->createFileBackup();
                $this->info("✅ Backup file dibuat: storage/app/$path");
            }

            $this->info("✅ Backup selesai pada " . now()->toDateTimeString());
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Gagal backup: ' . $e->getMessage());
            Log::error('BackupDataCommand failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 1;
        }
    }
}