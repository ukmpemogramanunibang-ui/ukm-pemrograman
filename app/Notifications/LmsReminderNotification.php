<?php

namespace App\Notifications;

use App\Models\Lms\LmsItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LmsReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public LmsItem $item) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id'    => $this->item->id,
            'item_title' => $this->item->title,
            'group_id'   => $this->item->group_id,
            'type'       => 'lms_reminder',
            'title'      => 'Pengingat Tugas',
            'message'    => 'Jangan lupa mengumpulkan: ' . $this->item->title,
            'url'        => route('owner.lms.items.show', $this->item->id),
        ];
    }
}