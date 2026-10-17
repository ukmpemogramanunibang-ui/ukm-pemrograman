<?php

namespace App\Notifications;

use App\Models\GroupAnnouncement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupAnnouncementPublished extends Notification
{
    use Queueable;

    public function __construct(public GroupAnnouncement $announcement) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title'           => $this->announcement->title,
            'message'         => str($this->announcement->content)->limit(160)->toString(),
            'type'            => $this->announcement->type,
            'priority'        => $this->announcement->priority,
            'action_url'      => route('owner.notifications.show', ['notification' => '__ID__']),
            'group_names'     => $this->announcement->groups->pluck('name')->take(3)->toArray(),
            'group_count'     => $this->announcement->groups->count(),
            'published_at'    => $this->announcement->published_at?->toIso8601String(),
            'expires_at'      => $this->announcement->expires_at?->toIso8601String(),
            'require_ack'     => $this->announcement->require_acknowledgement,
        ];
    }
}