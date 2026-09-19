<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IdentityVaultService;

class RecoverIdentities extends Command
{
    protected $signature = 'identity:recover {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Recover semua identitas dari vault ke database utama';

    public function handle(IdentityVaultService $vault)
    {
        if (!$this->option('force') && !$this->confirm('Yakin ingin recover semua identitas? Ini akan membuat ulang akun yang hilang.')) {
            return Command::FAILURE;
        }

        $this->info('Memulai recovery...');
        $results = $vault->recoverAllIdentities();

        $success = array_filter($results, fn($r) => $r === 'success');
        $failed = array_filter($results, fn($r) => $r !== 'success');

        $this->info('Recovery selesai.');
        $this->line('Berhasil: ' . count($success));
        $this->line('Gagal: ' . count($failed));

        foreach ($failed as $uuid => $error) {
            $this->error("$uuid : $error");
        }

        return Command::SUCCESS;
    }
}