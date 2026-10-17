<?php
// app/Traits/HasLinkedNotifications.php
namespace App\Traits;

use App\Services\Notifications\NotificationService;

trait HasLinkedNotifications
{
    public static function bootHasLinkedNotifications(): void
    {
        static::created(function ($m) {
            app(NotificationService::class)->syncEntity($m, 'created', $m->toNotificationPayload());
        });

        static::updated(function ($m) {
            // Skip kalau hanya kolom internal yg berubah
            if (! $m->notificationShouldSync()) return;
            app(NotificationService::class)->syncEntity($m, 'updated', $m->toNotificationPayload());
        });

        static::deleted(function ($m) {
            app(NotificationService::class)->syncEntity($m, 'deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($m) {
                app(NotificationService::class)->syncEntity($m, 'restored', $m->toNotificationPayload());
            });
        }
    }

    /** Override di model: kolom yg tidak memicu update notif */
    public function notificationShouldSync(): bool
    {
        $ignore = $this->notificationIgnoredColumns ?? [
            'updated_at', 'last_seen_at', 'view_count',
        ];
        $dirty = array_diff(array_keys($this->getDirty()), $ignore);
        return ! empty($dirty);
    }

    /** Wajib di-override di model */
    abstract public function toNotificationPayload(): array;

    /** Wajib di-override: user penerima notif */
    abstract public function notificationRecipients(): array;
}