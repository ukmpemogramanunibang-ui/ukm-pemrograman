<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\QrRecoveryService;
use Illuminate\Console\Command;

class GenerateRecoverySnapshots extends Command
{
    protected $signature = 'qr:generate-snapshots {--force : Generate ulang snapshot yang sudah ada}';
    protected $description = 'Generate recovery snapshot untuk semua user aktif';

    public function handle(QrRecoveryService $service)
    {
        $force = $this->option('force');
        $users = User::all();
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $count = 0;

        foreach ($users as $user) {
            if (!$force && $user->recoverySnapshot()->exists()) {
                $bar->advance();
                continue;
            }
            $service->generateSnapshot($user);
            $count++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Snapshot berhasil dibuat untuk $count user.");
    }
}