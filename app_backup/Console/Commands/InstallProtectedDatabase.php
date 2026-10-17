<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallProtectedDatabase extends Command
{
    protected $signature = 'protected:install';
    protected $description = 'Install protected database tables untuk backup/restore';

    public function handle()
    {
        $this->info('🔐 Installing protected database tables...');

        // Cek apakah database protected sudah ada
        try {
            DB::connection('mysql_protected')->getPdo();
        } catch (\Exception $e) {
            $this->error('❌ Koneksi ke database protected gagal. Pastikan database "ukm_unibang_protected" sudah dibuat.');
            $this->line('   Jalankan: mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ukm_unibang_protected"');
            return 1;
        }

        // Cari file migrasi
        $files = glob(database_path('migrations/*_create_protected_tables.php'));
        if (empty($files)) {
            $this->error('❌ File migrasi "create_protected_tables.php" tidak ditemukan.');
            $this->line('   Buat dulu dengan: php artisan make:migration create_protected_tables');
            $this->line('   Kemudian copy-paste isi migrasi dari dokumentasi.');
            return 1;
        }

        $file = basename($files[0]);
        $this->info("📂 Menjalankan migrasi: $file");

        // Jalankan migrasi ke database protected
        $exitCode = Artisan::call('migrate', [
            '--database' => 'mysql_protected',
            '--path' => 'database/migrations/' . $file,
            '--force' => true,
        ]);

        if ($exitCode === 0) {
            $this->info('✅ Protected database tables berhasil dibuat.');
            $this->info('🚀 Sekarang jalankan: php artisan backup:data --file');
            return 0;
        } else {
            $this->error('❌ Gagal membuat protected database tables.');
            $this->line(Artisan::output());
            return 1;
        }
    }
}