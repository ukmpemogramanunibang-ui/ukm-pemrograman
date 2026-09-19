<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar perintah Artisan yang terdaftar.
     *
     * @var array
     */
    protected $commands = [
        Commands\BackupDataCommand::class,
        Commands\RestoreDataCommand::class,
        Commands\InstallProtectedDatabase::class, // ✅ Tambahkan jika ada
        // Tambahkan command lain di sini
    ];

    /**
     * Definisi jadwal tugas.
     */
    protected function schedule(Schedule $schedule): void
    {
            $schedule->command('attendance:auto-sync')->everyThirtySeconds();
    $schedule->command('attendance:auto-close')->everyMinute();

        // ============================================================
        // 1. BACKUP DATA (sudah ada)
        // ============================================================
        $schedule->command('backup:data')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->then(function () {
                // Bisa kirim notifikasi sukses ke owner
                Log::info('Backup harian selesai.');
            })
            ->onFailure(function () {
                Log::error('Backup harian gagal!');
                // Kirim alert ke admin
            });

        $schedule->command('backup:data --file')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Backup sebelum migration (jika perlu)
        $schedule->command('backup:data --name=pre_migration_' . date('Ymd_His'))
            ->everyThirtyMinutes()
            ->when(function () {
                // Aktifkan hanya jika ada file migration baru – contoh sederhana
                return false; // sesuaikan dengan logika Anda
            });

        // ============================================================
        // 2. UPDATE STATUS BOOKING (dipindahkan dari Http Kernel)
        // ============================================================
        // Setiap menit: booking approved yang sudah lewat end_datetime → completed
        $schedule->call(function () {
            \App\Models\Booking::where('status', 'approved')
                ->where('end_datetime', '<', now())
                ->update(['status' => 'completed']);
        })->everyMinute()
          ->withoutOverlapping()
          ->name('update-bookings-to-completed')
          ->onFailure(function () {
              Log::error('Gagal update booking ke completed.');
          });

        // Setiap 5 menit: ghost booking (tidak check-in 30 menit setelah start) → expired
        $schedule->call(function () {
            $gracePeriod = 30; // menit
            \App\Models\Booking::where('status', 'approved')
                ->where('start_datetime', '<', now()->subMinutes($gracePeriod))
                ->whereNull('checkin_at')
                ->update(['status' => 'expired']);
        })->everyFiveMinutes()
          ->withoutOverlapping()
          ->name('expire-ghost-bookings')
          ->onFailure(function () {
              Log::error('Gagal meng-expire ghost booking.');
          });

        // ============================================================
        // 3. TUGAS TAMBAHAN (opsional)
        // ============================================================
        // Bersihkan log yang sudah tua setiap minggu
        $schedule->command('log:clear')
            ->weekly()
            ->withoutOverlapping();

        // Update status ruangan (jika diperlukan) – bisa dijalankan setiap jam
        $schedule->call(function () {
            // Misal: sinkronisasi status ruangan berdasarkan booking terakhir
        })->hourly()
          ->name('update-room-status');

        // Kirim laporan aktivitas harian ke owner (contoh)
        $schedule->command('report:daily')
            ->dailyAt('07:00')
            ->withoutOverlapping();
    }

    /**
     * Daftarkan command – HANYA muat file, jangan letakkan class di sini.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}