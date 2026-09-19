<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProtectFreshCommand extends Command
{
    protected $signature = 'migrate:fresh {--force : Jalankan meskipun di production}';
    protected $description = 'Proteksi migrate:fresh agar tidak berjalan di production';

    public function handle(): int   // ← ubah dari void ke int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('❌ DANGER: migrate:fresh dilarang pada environment production.');
            $this->warn('Gunakan --force jika Anda benar-benar yakin.');
            return 1; // error
        }

        // Jalankan migrate:fresh asli
        $this->call('migrate:fresh', ['--force' => true]);
        return 0; // sukses
    }
}