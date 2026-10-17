<?php

namespace App\Services\Notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationSource;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class NotificationService
{
    protected const CACHE_KEY_COLUMNS = 'notifications:columns';
    protected const CACHE_TTL_COLUMNS = 600; // 10 menit

    /** Kolom opsional — akan diskip jika tidak ada di DB */
    protected const OPTIONAL_COLUMNS = [
        'source', 'type_slug', 'priority', 'severity', 'title', 'icon',
        'action_url', 'actor_id', 'entity_type', 'entity_id', 'group_key',
        'requires_action', 'dedup_key', 'category',
        'related_id', 'related_type', 'event_type', 'version',
        'source_deleted_at', 'source_updated_at', 'fingerprint',
        'seen_at', 'read_at', 'starred_at', 'archived_at', 'actioned_at',
        'message', 'deep_link', 'priority',
    ];

    /** @var array<string,bool>|null Cache kolom per request */
    protected ?array $columnCache = null;

    /* ==========================================================
     | PUBLIC API — ENTRY POINT UTAMA
     * ========================================================== */

    /**
     * Kirim notifikasi ke satu user (practical wrapper).
     *
     * @return DatabaseNotification|null
     */
    public function sendToUser(
        User $user,
        string $title,
        string $message,
        string $source = 'system',
        array $options = []
    ): ?DatabaseNotification {
        $payload = array_merge([
            'title'   => $title,
            'message' => $message,
            'source'  => $source,
        ], $options);

        return $this->send($user, $payload, null, 'created');
    }

    /**
     * Sinkronisasi notifikasi berdasarkan event pada entity.
     * Dipanggil dari Observer model.
     */
    public function syncEntity(Model $model, string $event, ?array $payload = null): void
    {
        try {
            $payload ??= method_exists($model, 'toNotificationPayload')
                ? $model->toNotificationPayload()
                : $this->defaultPayload($model);

            $existing = $this->existingFor($model);

            match ($event) {
                'created'  => $this->handleCreated($model, $payload, $existing),
                'updated'  => $this->handleUpdated($model, $payload, $existing),
                'deleted'  => $this->handleDeleted($model, $existing),
                'restored' => $this->handleRestored($model, $payload, $existing),
                default    => null,
            };
        } catch (Throwable $e) {
            Log::warning('[Notif] syncEntity failed', [
                'model' => get_class($model),
                'id'    => $model->getKey(),
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kirim notifikasi ke satu user (core method).
     */
    public function send(
        User $user,
        array $payload,
        ?Model $model = null,
        string $event = 'created'
    ): ?DatabaseNotification {
        try {
            /* ---- 1. DEDUPLIKASI ---- */
            $dedup = $payload['dedup_key'] ?? null;
            if ($dedup && $this->existsDedup($user, $dedup)) {
                return null;
            }

            /* ---- 2. NORMALISASI ---- */
            $source   = $this->normalizeSource($payload['source'] ?? 'system');
            $priority = $this->normalizePriority($payload['priority'] ?? 'normal');
            $title    = $payload['title'] ?? 'Notifikasi';
            $message  = $payload['message'] ?? 'Anda memiliki notifikasi baru.';

            $data = $payload['data'] ?? [
                'title'   => $title,
                'message' => $message,
            ];

            /* ---- 3. BUAT INSTANCE ---- */
            $n = $this->newNotificationInstance();

            $n->id              = (string) Str::uuid();
            $n->type            = $payload['type'] ?? 'App\\Notifications\\UnicodeNotification';
            $n->notifiable_type = $user->getMorphClass();
            $n->notifiable_id   = $user->getKey();
            $n->data            = $data;
            $n->created_at      = now();
            $n->updated_at      = now();

            /* ---- 4. KOLOM OPSIONAL ---- */
            $this->setIfColumn($n, 'source',           $source);
            $this->setIfColumn($n, 'type_slug',        $payload['type_slug']   ?? null);
            $this->setIfColumn($n, 'priority',         $priority);
            $this->setIfColumn($n, 'severity',         $payload['severity']    ?? 'info');
            $this->setIfColumn($n, 'title',            $title);
            $this->setIfColumn($n, 'message',          $message);
            $this->setIfColumn($n, 'icon',             $payload['icon']        ?? 'bell');
            $this->setIfColumn($n, 'action_url',       $payload['action_url']  ?? null);
            $this->setIfColumn($n, 'actor_id',         $payload['actor_id']    ?? null);
            $this->setIfColumn($n, 'entity_type',      $payload['entity_type'] ?? null);
            $this->setIfColumn($n, 'entity_id',        $payload['entity_id']   ?? null);
            $this->setIfColumn($n, 'group_key',        $payload['group_key']   ?? null);
            $this->setIfColumn($n, 'requires_action',  (bool) ($payload['requires_action'] ?? false));
            $this->setIfColumn($n, 'dedup_key',        $dedup);
            $this->setIfColumn($n, 'category',         $payload['category']    ?? null);

            /* ---- 5. ENTITY TRACKING ---- */
            $relatedType = $payload['related_type']
                ?? ($model ? $model->getMorphClass() : null);
            $relatedId   = $payload['related_id']
                ?? ($model ? (string) $model->getKey() : null);

            $this->setIfColumn($n, 'related_type',      $relatedType);
            $this->setIfColumn($n, 'related_id',        $relatedId);
            $this->setIfColumn($n, 'event_type',        $event);
            $this->setIfColumn($n, 'version',           1);
            $this->setIfColumn($n, 'fingerprint',       $this->fingerprint($payload));
            $this->setIfColumn($n, 'source_updated_at', $model?->updated_at ?? now());

            $n->save();

            return $n;
        } catch (Throwable $e) {
            Log::warning('[Notif] send() failed', [
                'user_id' => $user->id ?? null,
                'title'   => $payload['title'] ?? null,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Bulk send — unique per user_id, pakai DB transaction.
     */
    public function sendMany(
        Collection|array $users,
        array $payload,
        ?Model $model = null,
        string $event = 'created'
    ): int {
        $sent = 0;
        $seen = [];

        foreach ($users as $user) {
            if (! $user instanceof User) continue;
            if (isset($seen[$user->id])) continue;
            $seen[$user->id] = true;

            if ($this->send($user, $payload, $model, $event) !== null) {
                $sent++;
            }
        }
        return $sent;
    }

    /* ==========================================================
     | EVENT HANDLERS (internal)
     * ========================================================== */

    protected function handleCreated(Model $model, array $payload, Collection $existing): void
    {
        if ($existing->isNotEmpty()) return; // idempotent

        foreach ($this->recipients($model) as $user) {
            if ($user instanceof User) {
                $this->send($user, $payload, $model, 'created');
            }
        }
    }

    protected function handleUpdated(Model $model, array $payload, Collection $existing): void
    {
        $fingerprint        = $this->fingerprint($payload);
        $currentRecipients  = collect($this->recipients($model))->pluck('id')->all();
        $existingRecipients = $existing->pluck('notifiable_id')->all();

        $hasFingerprint = $this->hasColumn('fingerprint');
        $hasVersion     = $this->hasColumn('version');

        /* ---- 1. Update notif yg sudah ada ---- */
        foreach ($existing as $row) {
            $rowFingerprint = $hasFingerprint ? ($row->fingerprint ?? null) : null;
            $contentChanged = $rowFingerprint !== $fingerprint;

            $updates = [
                'data'       => json_encode($payload),
                'updated_at' => now(),
            ];

            if ($this->hasColumn('title')) {
                $updates['title'] = $payload['title'] ?? ($row->title ?? null);
            }
            if ($this->hasColumn('message')) {
                $updates['message'] = $payload['message'] ?? ($row->message ?? null);
            }
            if ($this->hasColumn('event_type')) {
                $updates['event_type'] = 'updated';
            }
            if ($hasVersion) {
                $updates['version'] = ((int) ($row->version ?? 1)) + 1;
            }
            if ($hasFingerprint) {
                $updates['fingerprint'] = $fingerprint;
            }
            if ($this->hasColumn('source_updated_at')) {
                $updates['source_updated_at'] = $model->updated_at ?? now();
            }
            if ($this->hasColumn('source_deleted_at')) {
                $updates['source_deleted_at'] = null;
            }

            // Reset status kalau konten berubah
            if ($contentChanged) {
                if ($this->hasColumn('read_at'))     $updates['read_at']     = null;
                if ($this->hasColumn('seen_at'))     $updates['seen_at']     = null;
                if ($this->hasColumn('archived_at')) $updates['archived_at'] = null;
            }

            DB::table('notifications')->where('id', $row->id)->update($updates);
        }

        /* ---- 2. Penerima baru ---- */
        $added = array_diff($currentRecipients, $existingRecipients);
        foreach ($added as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->send($user, $payload, $model, 'updated');
            }
        }

        /* ---- 3. Penerima yg tidak lagi berhak → hapus ---- */
        $removed = array_diff($existingRecipients, $currentRecipients);
        if (! empty($removed)) {
            DB::table('notifications')
                ->where('related_type', $model->getMorphClass())
                ->where('related_id', (string) $model->getKey())
                ->whereIn('notifiable_id', $removed)
                ->delete();
        }
    }

    protected function handleDeleted(Model $model, Collection $existing): void
    {
        $updates = ['updated_at' => now()];

        if ($this->hasColumn('source_deleted_at')) $updates['source_deleted_at'] = now();
        if ($this->hasColumn('archived_at'))       $updates['archived_at']       = now();
        if ($this->hasColumn('event_type'))        $updates['event_type']        = 'deleted';

        DB::table('notifications')
            ->where('related_type', $model->getMorphClass())
            ->where('related_id', (string) $model->getKey())
            ->update($updates);
    }

    /**
     * ✅ FIXED — hilangkan unreachable code & handle restore dengan benar.
     */
    protected function handleRestored(Model $model, array $payload, Collection $existing): void
    {
        // Kalau belum ada notif sama sekali, treat seperti created
        if ($existing->isEmpty()) {
            $this->handleCreated($model, $payload, $existing);
            return;
        }

        $updates = [
            'data'       => json_encode($payload),
            'updated_at' => now(),
        ];

        if ($this->hasColumn('source_deleted_at')) $updates['source_deleted_at'] = null;
        if ($this->hasColumn('archived_at'))       $updates['archived_at']       = null;
        if ($this->hasColumn('read_at'))           $updates['read_at']           = null;
        if ($this->hasColumn('seen_at'))           $updates['seen_at']           = null;
        if ($this->hasColumn('event_type'))        $updates['event_type']        = 'restored';
        if ($this->hasColumn('title'))             $updates['title']             = $payload['title'] ?? null;
        if ($this->hasColumn('message'))           $updates['message']           = $payload['message'] ?? null;
        if ($this->hasColumn('fingerprint'))       $updates['fingerprint']       = $this->fingerprint($payload);
        if ($this->hasColumn('version'))           $updates['version']           = DB::raw('version + 1');

        DB::table('notifications')
            ->where('related_type', $model->getMorphClass())
            ->where('related_id', (string) $model->getKey())
            ->update($updates);
    }

    /* ==========================================================
     | STATE CHANGES
     * ========================================================== */

    public function markSeen(DatabaseNotification $n): void
    {
        if ($this->hasColumn('seen_at') && is_null($n->seen_at ?? null)) {
            $n->forceFill(['seen_at' => now()])->save();
        }
    }

    public function markRead(DatabaseNotification $n): void
    {
        $now     = now();
        $updates = [];

        if ($this->hasColumn('seen_at') && is_null($n->seen_at ?? null)) {
            $updates['seen_at'] = $now;
        }
        if ($this->hasColumn('read_at') && is_null($n->read_at ?? null)) {
            $updates['read_at'] = $now;
        }

        if (! empty($updates)) {
            $n->forceFill($updates)->save();

            // Sync counter di pengumuman (kalau notif dari group announcement)
            $this->syncAnnouncementReadCounter($n);
        }
    }

    public function markActioned(DatabaseNotification $n): void
    {
        if ($this->hasColumn('actioned_at')) {
            $n->forceFill(['actioned_at' => now()])->save();
        }
    }

    public function toggleStar(DatabaseNotification $n): void
    {
        if (! $this->hasColumn('starred_at')) return;
        $n->forceFill([
            'starred_at' => ($n->starred_at ?? null) ? null : now(),
        ])->save();
    }

    public function archive(DatabaseNotification $n): void
    {
        if (! $this->hasColumn('archived_at')) return;
        $n->forceFill(['archived_at' => now()])->save();
    }

    public function unarchive(DatabaseNotification $n): void
    {
        if (! $this->hasColumn('archived_at')) return;
        $n->forceFill(['archived_at' => null])->save();
    }

    /* ==========================================================
     | QUERIES
     * ========================================================== */

    public function unreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    public function existsDedup(User $user, string $dedupKey): bool
    {
        if (! $this->hasColumn('dedup_key')) return false;

        return $this->notificationQuery()
            ->where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->getKey())
            ->where('dedup_key', $dedupKey)
            ->exists();
    }

    /* ==========================================================
     | GARBAGE COLLECTOR
     * ========================================================== */

    public function pruneOrphans(int $chunkSize = 200): int
    {
        $deleted = 0;

        DB::table('notifications')
            ->whereNotNull('related_type')
            ->whereNull('source_deleted_at')
            ->orderBy('id')
            ->chunk($chunkSize, function ($rows) use (&$deleted) {
                foreach ($rows as $row) {
                    $class = $row->related_type;
                    if (! class_exists($class)) continue;

                    try {
                        $query = method_exists($class, 'withTrashed')
                            ? $class::withTrashed()
                            : $class::query();

                        $exists = $query->find($row->related_id);
                        if ($exists) continue;

                        $updates = ['updated_at' => now()];
                        if ($this->hasColumn('source_deleted_at')) $updates['source_deleted_at'] = now();
                        if ($this->hasColumn('archived_at'))       $updates['archived_at']       = now();
                        if ($this->hasColumn('event_type'))        $updates['event_type']        = 'deleted';

                        DB::table('notifications')->where('id', $row->id)->update($updates);
                        $deleted++;
                    } catch (Throwable $e) {
                        Log::warning('[Notif] pruneOrphans skip', [
                            'id'    => $row->id,
                            'class' => $class,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

        return $deleted;
    }

    /* ==========================================================
     | INTERNAL HELPERS
     * ========================================================== */

    /**
     * Sync counter read/ack di GroupAnnouncement (kalau notif dari group_announcement).
     */
    protected function syncAnnouncementReadCounter(DatabaseNotification $n): void
    {
        try {
            if (($n->source ?? null) !== 'group_announcement') return;

            $data = is_array($n->data) ? $n->data : json_decode($n->data ?? '[]', true);
            $announcementId = $data['announcement_id'] ?? null;

            if (! $announcementId) return;

            if (class_exists(\App\Models\GroupAnnouncement::class)) {
                \App\Models\GroupAnnouncement::where('id', $announcementId)
                    ->increment('read_count');
            }
        } catch (Throwable $e) {
            Log::debug('[Notif] syncAnnouncementReadCounter skipped', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function existingFor(Model $model): Collection
    {
        return DB::table('notifications')
            ->where('related_type', $model->getMorphClass())
            ->where('related_id', (string) $model->getKey())
            ->get();
    }

    protected function recipients(Model $model): array
    {
        if (method_exists($model, 'notificationRecipients')) {
            $r = $model->notificationRecipients();
            return is_array($r) ? $r : $r->all();
        }
        return [];
    }

    protected function fingerprint(array $payload): string
    {
        return hash('sha256', json_encode([
            'title'      => $payload['title']      ?? '',
            'message'    => $payload['message']    ?? '',
            'status'     => $payload['status']     ?? '',
            'action_url' => $payload['action_url'] ?? '',
        ]));
    }

    protected function defaultPayload(Model $model): array
    {
        return [
            'title'   => class_basename($model) . ' #' . $model->getKey(),
            'message' => 'Data telah diperbarui.',
            'source'  => Str::snake(class_basename($model)),
        ];
    }

    protected function newNotificationInstance(): DatabaseNotification
    {
        if (class_exists(\App\Models\Notification::class)
            && is_subclass_of(\App\Models\Notification::class, DatabaseNotification::class)) {
            return new \App\Models\Notification();
        }
        return new DatabaseNotification();
    }

    protected function notificationQuery()
    {
        if (class_exists(\App\Models\Notification::class)) {
            return \App\Models\Notification::query();
        }
        return DatabaseNotification::query();
    }

    protected function setIfColumn(DatabaseNotification $n, string $col, $value): void
    {
        if ($this->hasColumn($col)) {
            $n->{$col} = $value;
        } elseif ($col === 'title' && is_string($value)) {
            $data = is_array($n->data) ? $n->data : [];
            $data['title'] = $value;
            $n->data = $data;
        }
    }

    protected function hasColumn(string $col): bool
    {
        if ($this->columnCache === null) {
            $this->columnCache = Cache::remember(
                self::CACHE_KEY_COLUMNS,
                now()->addSeconds(self::CACHE_TTL_COLUMNS),
                fn () => array_flip(Schema::getColumnListing('notifications'))
            );
        }
        return isset($this->columnCache[$col]);
    }

    public function flushColumnCache(): void
    {
        $this->columnCache = null;
        Cache::forget(self::CACHE_KEY_COLUMNS);
    }

    /* ==========================================================
     | NORMALIZERS
     * ========================================================== */

    protected function normalizeSource(string|NotificationSource $source): string
    {
        if ($source instanceof NotificationSource) return $source->value;

        if (enum_exists(NotificationSource::class)) {
            $values = method_exists(NotificationSource::class, 'values')
                ? NotificationSource::values()
                : array_column(NotificationSource::cases(), 'value');
            return in_array($source, $values, true) ? $source : 'system';
        }
        return $source ?: 'system';
    }

    protected function normalizePriority(string|NotificationPriority $priority): string
    {
        if ($priority instanceof NotificationPriority) return $priority->value;

        if (enum_exists(NotificationPriority::class)) {
            $values = method_exists(NotificationPriority::class, 'values')
                ? NotificationPriority::values()
                : array_column(NotificationPriority::cases(), 'value');
            return in_array($priority, $values, true) ? $priority : 'normal';
        }
        return $priority ?: 'normal';
    }
}