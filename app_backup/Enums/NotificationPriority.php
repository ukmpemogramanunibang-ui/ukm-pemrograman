<?php

namespace App\Enums;

enum NotificationPriority: string
{
    case Low    = 'low';
    case Normal = 'normal';
    case High   = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return config("unicode_notifications.priorities.{$this->value}.label", ucfirst($this->value));
    }

    public function color(): string
    {
        return config("unicode_notifications.priorities.{$this->value}.color", 'gray');
    }

    public function weight(): int
    {
        return config("unicode_notifications.priorities.{$this->value}.weight", 1);
    }
}
