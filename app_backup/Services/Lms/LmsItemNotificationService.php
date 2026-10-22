<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsItem;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class LmsItemNotificationService
{
    public function __construct(
        protected NotificationService $notifier
    ) {}

    /* =========================================================
     | DISPATCH — Buat notifikasi untuk semua target
     * ========================================================= */
    public function dispatch(LmsItem $item): array
    {
        $recipientIds = $this->resolveRecipients($item);

        if (empty($recipientIds)) {
            $this->updateCounter($item, 0);
            return [
                'recipient_count'    => 0,
                'notification_count' => 0,
            ];
        }

        $users = User::whereIn('id', $recipientIds)->get();
        $payload = $this->buildPayload($item);

        $sent = 0;

        foreach ($users as $user) {
            try {
                $result = $this->notifier->send(
                    $user,
                    array_merge($payload, [
                        'dedup_key' => $this->dedupKey($item, $user),
                    ]),
                    $item,
                    'created'
                );

                if ($result !== null) $sent++;
            } catch (Throwable $e) {
                Log::warning('[LMS] Failed to send notif', [
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $this->updateCounter($item, $sent);

        return [
            'recipient_count'    => count($recipientIds),
            'notification_count' => $sent,
        ];
    }

    /* =========================================================
     | SYNC — Update notif yang sudah ada + adjust recipients
     * ========================================================= */
    public function sync(LmsItem $item): void
    {
        $currentRecipients  = $this->resolveRecipients($item);
        $existingRecipients = $this->existingRecipients($item);

        $payload = $this->buildPayload($item);

        /* ---- 1. Update semua notif existing (title, message, action) ---- */
        DB::table('notifications')
            ->where('entity_type', LmsItem::class)
            ->where('entity_id', $item->id)
            ->update([
                'title'      => $payload['title'],
                'message'    => $payload['message'],
                'action_url' => $payload['action_url'],
                'data'       => json_encode($payload['data']),
                'updated_at' => now(),
            ]);

        /* ---- 2. Penerima baru → kirim notif ---- */
        $added = array_diff($currentRecipients, $existingRecipients);
        foreach ($added as $uid) {
            $user = User::find($uid);
            if (! $user) continue;

            try {
                $this->notifier->send(
                    $user,
                    array_merge($payload, [
                        'dedup_key' => $this->dedupKey($item, $user),
                    ]),
                    $item,
                    'created'
                );
            } catch (Throwable $e) {
                Log::warning('[LMS] Failed to add recipient', [
                    'item_id' => $item->id,
                    'user_id' => $uid,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        /* ---- 3. Penerima yang tidak lagi berhak → hapus notif ---- */
        $removed = array_diff($existingRecipients, $currentRecipients);
        if (! empty($removed)) {
            DB::table('notifications')
                ->where('entity_type', LmsItem::class)
                ->where('entity_id', $item->id)
                ->whereIn('user_id', $removed)
                ->delete();
        }

        $this->updateCounter($item, count($currentRecipients));
    }

    /* =========================================================
     | PURGE — Hapus semua notif terkait item (dipanggil saat delete)
     * ========================================================= */
    public function purge(LmsItem $item): int
    {
        return DB::table('notifications')
            ->where('entity_type', LmsItem::class)
            ->where('entity_id', $item->id)
            ->delete();
    }

    /* =========================================================
     | RESOLVE RECIPIENTS
     * ========================================================= */
    public function resolveRecipients(LmsItem $item): array
    {
        $targetMode = $item->target_mode ?? 'role';

        $roleIds = $this->parseIds($item->target_roles);
        $userIds = $this->parseIds($item->target_user_ids);

        // Fallback: baca dari tabel targets kalau ada
        if (empty($roleIds) && empty($userIds) && method_exists($item, 'targets')) {
            try {
                $targets = $item->targets()->get();
                $roleIds = $targets->where('target_type', 'role')->pluck('target_id')->filter()->toArray();
                $userIds = $targets->where('target_type', 'user')->pluck('target_id')->filter()->toArray();
            } catch (Throwable $e) {
                // ignore
            }
        }

        $fromRoles = [];
        if (in_array($targetMode, ['role', 'both'], true) && ! empty($roleIds)) {
            $fromRoles = User::query()
                ->whereIn('level_id', $roleIds)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();
        }

        $fromUsers = [];
        if (in_array($targetMode, ['individual', 'both'], true) && ! empty($userIds)) {
            $fromUsers = User::query()
                ->whereIn('id', $userIds)
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();
        }

        // Merge + dedupe
        $recipients = array_unique(array_merge($fromRoles, $fromUsers));

        // Exclude creator sendiri
        $creatorId = $item->created_by ?? null;
        if ($creatorId) {
            $recipients = array_filter($recipients, fn ($id) => $id !== $creatorId);
        }

        return array_values($recipients);
    }

    /* =========================================================
     | INTERNAL HELPERS
     * ========================================================= */
    protected function buildPayload(LmsItem $item): array
    {
        $isTask = $item->type === 'task';
        $emoji  = $isTask ? '📝' : '📚';

        $title = $isTask
            ? "{$emoji} Tugas Baru: {$item->title}"
            : "{$emoji} Materi Baru: {$item->title}";

        $message = Str::limit(strip_tags($item->description ?? ''), 150)
            ?: ($isTask ? 'Ada tugas baru untuk kamu. Cek segera!' : 'Ada materi baru untuk kamu.');

        return [
            'title'       => $title,
            'message'     => $message,
            'source'      => 'lms',
            'icon'        => $isTask ? 'clipboard-list' : 'book-open',
            'priority'    => $isTask ? 'high' : 'normal',
            'category'    => 'academic',
            'action_url'  => route('owner.lms.items.show', $item->id),
            'entity_type' => LmsItem::class,
            'entity_id'   => $item->id,
            'data' => [
                'title'          => $title,
                'message'        => $message,
                'item_id'        => $item->id,
                'item_type'      => $item->type,
                'item_title'     => $item->title,
                'deadline_at'    => $item->deadline_at?->toIso8601String(),
                'group_name'     => $item->group?->name,
                'has_attachment' => ! empty($item->attachments),
                'video_url'      => $item->video_url,
            ],
        ];
    }

    protected function dedupKey(LmsItem $item, User $user): string
    {
        return "lms_item:{$item->id}:user:{$user->id}";
    }

    protected function updateCounter(LmsItem $item, int $count): void
    {
        try {
            $updates = [];
            if (\Schema::hasColumn('lms_items', 'recipients_count')) {
                $updates['recipients_count'] = $count;
            }
            if (\Schema::hasColumn('lms_items', 'recipient_count')) {
                $updates['recipient_count'] = $count;
            }
            if (! empty($updates)) {
                $item->updateQuietly($updates);
            }
        } catch (Throwable $e) {
            // ignore
        }
    }

    protected function existingRecipients(LmsItem $item): array
    {
        return DB::table('notifications')
            ->where('entity_type', LmsItem::class)
            ->where('entity_id', $item->id)
            ->pluck('user_id')
            ->filter()
            ->unique()
            ->toArray();
    }

    protected function parseIds($value): array
    {
        if (is_array($value)) return $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
    public function notifyPublished(LmsItem $item): void
    {
        // Stub. Silakan isi notifikasi ke recipient.
    }
}