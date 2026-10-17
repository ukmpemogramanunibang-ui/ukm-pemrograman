<?php

namespace App\Console;

use App\Jobs\PublishScheduledAnnouncements;
use App\Models\Booking;
use App\Models\GroupAnnouncement;
use App\Services\Notifications\NotificationService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /* ============================================================
     | COMMANDS
     * ============================================================ */
    protected $commands = [
        \App\Console\Commands\PruneNotifications::class,
        \App\Console\Commands\SyncEntityNotifications::class,
    ];

    /* ============================================================
     | SCHEDULE — Semua cron di sini
     * ============================================================ */
    protected function schedule(Schedule $schedule): void
    {
        /* =========================================================
         | BOOKING LIFECYCLE
         * ========================================================= */

        // Setiap menit: booking yang sudah lewat end_datetime → completed
        $schedule->call(function () {
            Booking::where('status', 'approved')
                ->where('end_datetime', '<', now())
                ->update(['status' => 'completed']);
        })
            ->everyMinute()
            ->name('booking:auto-complete')
            ->withoutOverlapping();

        // Setiap 5 menit: booking yang tidak check-in dalam grace period → expired
        $schedule->call(function () {
            $gracePeriod = (int) config('booking.checkin_grace_minutes', 30);

            Booking::where('status', 'approved')
                ->where('start_datetime', '<', now()->subMinutes($gracePeriod))
                ->whereNull('checkin_at')
                ->update(['status' => 'expired']);
        })
            ->everyFiveMinutes()
            ->name('booking:auto-expire')
            ->withoutOverlapping();


        /* =========================================================
         | ANNOUNCEMENT LIFECYCLE
         * ========================================================= */

        // Setiap menit: publish pengumuman terjadwal yang sudah waktunya
        $schedule->job(new PublishScheduledAnnouncements)
            ->everyMinute()
            ->name('announcement:publish-scheduled')
            ->withoutOverlapping();

        // Setiap 15 menit: tandai pengumuman yang sudah lewat expires_at → expired
        $schedule->call(function () {
            $affected = GroupAnnouncement::query()
                ->where('status', 'published')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);

            if ($affected > 0) {
                Log::info("Marked {$affected} announcements as expired");
            }
        })
            ->everyFifteenMinutes()
            ->name('announcement:auto-expire')
            ->withoutOverlapping();

        // Setiap jam: sinkronisasi counter read_count & ack_count dari tabel notifications
        $schedule->call(function () {
            GroupAnnouncement::query()
                ->where('status', 'published')
                ->where('published_at', '>=', now()->subDays(7))  // hanya 7 hari terakhir
                ->chunkById(50, function ($announcements) {
                    foreach ($announcements as $ann) {
                        $readCount = DB::table('notifications')
                            ->where('notifiable_type', \App\Models\User::class)
                            ->where('data->announcement_id', $ann->id)
                            ->whereNotNull('read_at')
                            ->count();

                        $totalCount = DB::table('notifications')
                            ->where('notifiable_type', \App\Models\User::class)
                            ->where('data->announcement_id', $ann->id)
                            ->count();

                        $ann->updateQuietly([
                            'recipient_count' => $totalCount,
                            'read_count'      => $readCount,
                        ]);
                    }
                });
        })
            ->hourly()
            ->name('announcement:sync-counters')
            ->withoutOverlapping();


        /* =========================================================
         | NOTIFICATIONS
         * ========================================================= */

        // Setiap jam: bersihkan notifikasi yatim (entity sudah dihapus)
        $schedule->call(function () {
            $count = app(NotificationService::class)->pruneOrphans();
            Log::info("Pruned {$count} orphan notifications");
        })
            ->hourly()
            ->name('notifications:prune-orphans')
            ->withoutOverlapping();

        // Setiap hari 02:00: hapus notifikasi yang sudah dibaca > 30 hari
        $schedule->call(function () {
            $deleted = DB::table('notifications')
                ->whereNotNull('read_at')
                ->where('read_at', '<', now()->subDays(30))
                ->delete();

            Log::info("Deleted {$deleted} read notifications older than 30 days");
        })
            ->dailyAt('02:00')
            ->name('notifications:gc-read')
            ->withoutOverlapping()
            ->timezone('Asia/Jakarta');

        // Setiap hari 03:30: hapus notifikasi arsip > 90 hari
        $schedule->call(function () {
            $deleted = DB::table('notifications')
                ->whereNotNull('archived_at')
                ->where('archived_at', '<', now()->subDays(90))
                ->delete();

            Log::info("Deleted {$deleted} archived notifications older than 90 days");
        })
            ->dailyAt('03:30')
            ->name('notifications:gc-archived')
            ->withoutOverlapping()
            ->timezone('Asia/Jakarta');


        /* =========================================================
         | SYSTEM MAINTENANCE
         * ========================================================= */

        // Setiap hari 03:00: backup database ringan
        $schedule->command('backup:run --only-db')
            ->dailyAt('03:00')
            ->name('db:backup')
            ->onOneServer()
            ->timezone('Asia/Jakarta');

        // Setiap hari 04:00: bersihkan session & cache kadaluwarsa
        $schedule->command('cache:prune-stale-tags')
            ->dailyAt('04:00')
            ->name('cache:prune-stale')
            ->timezone('Asia/Jakarta');

        $schedule->command('session:prune')
            ->dailyAt('04:15')
            ->name('session:prune')
            ->timezone('Asia/Jakarta');

        // Setiap Minggu 03:30: vacuum database (opsional)
        // $schedule->exec('mysqlcheck -o --all-databases')
        //     ->weeklyOn(0, '03:30')
        //     ->name('db:optimize');


        /* =========================================================
         | REPORTING
         * ========================================================= */

        // Setiap Senin 09:00: laporan mingguan owner
        $schedule->command('report:weekly-owner')
            ->weeklyOn(1, '09:00')
            ->name('report:weekly')
            ->timezone('Asia/Jakarta');

        // Setiap awal bulan 08:00: laporan bulanan
        $schedule->command('report:monthly')
            ->monthlyOn(1, '08:00')
            ->name('report:monthly')
            ->timezone('Asia/Jakarta');


        /* =========================================================
         | HEALTH CHECKS (opsional)
         * ========================================================= */

        // Setiap 5 menit: cek queue health
        $schedule->call(function () {
            $size = DB::table('jobs')->count();
            if ($size > 1000) {
                Log::warning("Queue backlog: {$size} jobs pending");
            }
        })
            ->everyFiveMinutes()
            ->name('queue:health-check')
            ->withoutOverlapping();
    }

    /* ============================================================
     | COMMANDS REGISTRATION
     * ============================================================ */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}