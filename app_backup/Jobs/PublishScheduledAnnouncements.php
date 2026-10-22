<?php

namespace App\Jobs;

use App\Models\GroupAnnouncement;
use App\Services\GroupAnnouncementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PublishScheduledAnnouncements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(GroupAnnouncementService $service): void
    {
        $due = GroupAnnouncement::query()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($due as $announcement) {
            try {
                $announcement->update([
                    'status'       => 'published',
                    'published_at' => now(),
                ]);

                $service->dispatchNotifications($announcement);
            } catch (\Throwable $e) {
                \Log::error('Failed to publish scheduled announcement', [
                    'id' => $announcement->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}