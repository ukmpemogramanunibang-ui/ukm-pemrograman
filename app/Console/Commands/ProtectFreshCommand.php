<?php

namespace App\Console\Commands;

use Illuminate\Database\Console\Migrations\FreshCommand;

class ProtectFreshCommand extends FreshCommand
{
    /**
     * Signature, description, dan SEMUA option diwarisi dari FreshCommand.
     * --drop-views, --drop-types, --seed, --seeder, --step, --path, dll.
     * otomatis tersedia → RefreshDatabase bisa jalan normal.
     *
     * Parent (FreshCommand) sudah punya proteksi production via ConfirmableTrait.
     * Override handle() hanya untuk pesan error kustom.
     */
public function handle()
{
    // Logika custom Anda
    \Log::warning('migrate:fresh dipanggil', ['user' => auth()->id()]);

    // Proteksi production
    if ($this->laravel->isProduction() && ! $this->option('force')) {
        $this->components->error('Diblokir di production.');
        return self::FAILURE;
    }

    // Delegasi ke framework — semua option --drop-views, --seed, dll. tetap bekerja
    return parent::handle();
}
}