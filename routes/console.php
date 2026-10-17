<?php

use App\Jobs\PublishScheduledAnnouncements;
use App\Models\Booking;
use App\Models\GroupAnnouncement;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/* ============================================================
 | BOOKING LIFECYCLE
 * ============================================================ */

Schedule::call(function () {
    Booking::where('status', 'approved')
        ->where('end_datetime', '<', now())
        ->update(['status' => 'completed']);
})
    ->everyMinute()
    ->name('booking:auto-complete')
    ->withoutOverlapping();

Schedule::call(function () {
    $grace = (int) config('booking.checkin_grace_minutes', 30);
    Booking::where('status', 'approved')
        ->where('start_datetime', '<', now()->subMinutes($grace))
        ->whereNull('checkin_at')
        ->update(['status' => 'expired']);
})
    ->everyFiveMinutes()
    ->name('booking:auto-expire')
    ->withoutOverlapping();


/* ============================================================
 | ANNOUNCEMENT LIFECYCLE
 * ============================================================ */

Schedule::job(new PublishScheduledAnnouncements)
    ->everyMinute()
    ->name('announcement:publish-scheduled')
    ->withoutOverlapping();

Schedule::call(function () {
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


/* ============================================================
 | NOTIFICATIONS MAINTENANCE
 * ============================================================ */

Schedule::call(function () {
    $deleted = DB::table('notifications')
        ->whereNotNull('read_at')
        ->where('read_at', '<', now()->subDays(30))
        ->delete();

    Log::info("Deleted {$deleted} old read notifications");
})
    ->dailyAt('02:00')
    ->name('notifications:gc-read')
    ->withoutOverlapping()
    ->timezone('Asia/Jakarta');


/* ============================================================
 | DATABASE MAINTENANCE
 * ============================================================ */

Schedule::command('backup:run --only-db')
    ->dailyAt('03:00')
    ->name('db:backup')
    ->onOneServer()
    ->timezone('Asia/Jakarta');