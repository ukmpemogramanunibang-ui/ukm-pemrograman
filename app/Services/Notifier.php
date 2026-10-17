<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;

class Notifier
{
    /** Kirim ke semua Ketua */
    public static function toKetua(array $data): void
    {
        User::where('level_id', 3)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->each(fn ($id) => UserNotification::pushTo((int) $id, $data));
    }

    /** Kirim ke semua Pengurus */
    public static function toPengurus(array $data): void
    {
        User::where('level_id', 4)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->each(fn ($id) => UserNotification::pushTo((int) $id, $data));
    }

    /** Kirim ke semua Owner */
    public static function toOwner(array $data): void
    {
        User::where('level_id', 1)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->each(fn ($id) => UserNotification::pushTo((int) $id, $data));
    }

    /** Kirim ke satu user */
    public static function toUser(int $userId, array $data): void
    {
        UserNotification::pushTo($userId, $data);
    }
}