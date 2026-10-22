<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Services\IdentityVaultService;

class ResetAndRecover extends Command
{
    protected $signature = 'system:reset-database {--seed}';
    protected $description = 'Reset database utama dan recovery identitas dari vault';

    public function handle(IdentityVaultService $vault)
    {
        // Konfirmasi
        if (!$this->confirm('WARNING! Ini akan menghapus seluruh data di database utama. Lanjutkan?', false)) {
            return Command::FAILURE;
        }

        // 1. Fresh migrate
        $this->info('Menjalankan migrate:fresh...');
        Artisan::call('migrate:fresh', ['--force' => true]);
        $this->info(Artisan::output());

        // 2. Seed jika diminta
        if ($this->option('seed')) {
            $this->info('Menjalankan seed...');
            Artisan::call('db:seed', ['--force' => true]);
            $this->info(Artisan::output());
        }

        // 3. Recovery identitas
        $this->info('Memulai recovery identitas dari vault...');
        $results = $vault->recoverAllIdentities();

        $success = array_filter($results, fn($r) => $r === 'success');
        $failed = array_filter($results, fn($r) => $r !== 'success');

        $this->info("Recovery selesai: Berhasil " . count($success) . ", Gagal " . count($failed));
        foreach ($failed as $uuid => $error) {
            $this->error("$uuid : $error");
        }

        $this->info('Sistem siap digunakan. QR dan NFC tetap valid.');

        return Command::SUCCESS;
    }
}