<?php
// app/Console/Commands/TestNotifyRestore.php
namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\Notifications\NotificationService;
use Illuminate\Console\Command;

class TestNotifyRestore extends Command
{
    protected $signature = 'notify:test-restore {--id=}';
    protected $description = 'Test restore notification flow';

    public function handle(NotificationService $svc): int
    {
        $id = $this->option('id') ?: Booking::withTrashed()->latest('id')->value('id');
        $booking = Booking::withTrashed()->find($id);

        if (! $booking) {
            $this->error("Booking #{$id} not found.");
            return self::FAILURE;
        }

        $this->info("Testing restore for Booking #{$id}...");
        $svc->syncEntity($booking, 'restored', $booking->toNotificationPayload());

        $this->info('✅ Restore notification dispatched.');
        return self::SUCCESS;
    }
}