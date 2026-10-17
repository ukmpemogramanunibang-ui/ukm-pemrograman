<?php

namespace App\Observers;

use App\Models\Lms\LmsItem;
use App\Services\Lms\LmsItemNotificationService;
use Illuminate\Support\Facades\Log;
use Throwable;

class LmsItemObserver
{
    public function __construct(
        protected LmsItemNotificationService $service
    ) {}

    /* =========================================================
     | CREATED — Dispatch notifikasi
     * ========================================================= */
    public function created(LmsItem $item): void
    {
        if (! $item->isPublished()) {
            return;
        }

        $this->safely(function () use ($item) {
            $result = $this->service->dispatch($item);

            Log::info('[LMS Observer] Notif dispatched on created', [
                'item_id'         => $item->id,
                'title'           => $item->title,
                'recipient_count' => $result['recipient_count'] ?? 0,
                'sent'            => $result['notification_count'] ?? 0,
            ]);
        }, $item, 'created');
    }

    /* =========================================================
     | UPDATED — Sync (recipient bisa berubah)
     * ========================================================= */
    public function updated(LmsItem $item): void
    {
        // Hanya sync kalau field yang relevan berubah
        $changes = array_keys($item->getChanges());
        $relevant = [
            'title', 'description', 'deadline_at',
            'target_mode', 'target_roles', 'target_user_ids',
            'status', 'group_id', 'attachments',
        ];

        if (empty(array_intersect($changes, $relevant))) {
            return;
        }

        // Kalau status berubah dari published → draft, purge notif
        if ($item->isDirty('status') && ! $item->isPublished()) {
            $this->safely(function () use ($item) {
                $count = $this->service->purge($item);
                Log::info('[LMS Observer] Notif purged on unpublish', [
                    'item_id' => $item->id,
                    'count'   => $count,
                ]);
            }, $item, 'unpublished');
            return;
        }

        // Kalau status draft → published, dispatch baru
        if ($item->isDirty('status') && $item->isPublished() && $item->getOriginal('status') !== 'published') {
            $this->safely(function () use ($item) {
                $result = $this->service->dispatch($item);
                Log::info('[LMS Observer] Notif dispatched on publish', [
                    'item_id'         => $item->id,
                    'recipient_count' => $result['recipient_count'] ?? 0,
                ]);
            }, $item, 'published');
            return;
        }

        // Default: sync
        $this->safely(fn () => $this->service->sync($item), $item, 'updated');
    }

    /* =========================================================
     | DELETED — Purge (works for both soft & hard delete)
     * ========================================================= */
    public function deleted(LmsItem $item): void
    {
        $this->safely(function () use ($item) {
            $count = $this->service->purge($item);

            Log::info('[LMS Observer] Notif purged on delete', [
                'item_id'     => $item->id,
                'count'       => $count,
                'soft_delete' => ! $item->isForceDeleting(),
            ]);
        }, $item, 'deleted');
    }

    /* =========================================================
     | FORCE DELETED — Purge untuk hard delete permanen
     * ========================================================= */
    public function forceDeleted(LmsItem $item): void
    {
        $this->safely(function () use ($item) {
            $count = $this->service->purge($item);

            Log::info('[LMS Observer] Notif purged on force delete', [
                'item_id' => $item->id,
                'count'   => $count,
            ]);
        }, $item, 'forceDeleted');
    }

    /* =========================================================
     | RESTORED — Optional: re-dispatch kalau item di-restore
     * ========================================================= */
    public function restored(LmsItem $item): void
    {
        if (! $item->isPublished()) {
            return;
        }

        $this->safely(function () use ($item) {
            $result = $this->service->dispatch($item);

            Log::info('[LMS Observer] Notif re-dispatched on restore', [
                'item_id' => $item->id,
                'sent'    => $result['notification_count'] ?? 0,
            ]);
        }, $item, 'restored');
    }

    /* =========================================================
     | HELPER — Safe wrapper
     * ========================================================= */
    protected function safely(callable $fn, LmsItem $item, string $event): void
    {
        try {
            $fn();
        } catch (Throwable $e) {
            Log::warning("[LMS Observer] Failed on {$event}", [
                'item_id' => $item->id,
                'title'   => $item->title,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }
}