<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Notifications\DatabaseNotification|null send(\App\Models\User $user, array $payload)
 * @method static int sendMany($users, array $payload)
 * @method static void markSeen(\Illuminate\Notifications\DatabaseNotification $n)
 * @method static void markRead(\Illuminate\Notifications\DatabaseNotification $n)
 * @method static void markActioned(\Illuminate\Notifications\DatabaseNotification $n)
 * @method static int unreadCount(\App\Models\User $user)
 *
 * @see \App\Services\Notifications\NotificationService
 */
class Notify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\Notifications\NotificationService::class;
    }
}
