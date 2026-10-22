<?php

namespace App\Enums;

enum NotificationSource: string
{
    case Tasks       = 'tasks';
    case Materials   = 'materials';
    case Submissions = 'submissions';
    case Attendance  = 'attendance';
    case Schedules   = 'schedules';
    case Messages    = 'messages';
    case Game        = 'game';
    case Divisi      = 'divisi';
    case Officials   = 'officials';
    case Rooms       = 'rooms';
    case System      = 'system';
     case LMS        = 'lms';
    case FINANCIAL  = 'financial';
    case CHAT       = 'chat';
    case ANNOUNCE   = 'announce';
    case BOOKING    = 'booking';
    case MEMBER     = 'member';
    case SECURITY   = 'security';

    public function label(): string
    {
        return config("unicode_notifications.sources.{$this->value}.label", ucfirst($this->value));
    }

    public function icon(): string
    {
        return config("unicode_notifications.sources.{$this->value}.icon", 'fa-bell');
    }

    public function color(): string
    {
        return config("unicode_notifications.sources.{$this->value}.color", 'gray');
    }

    public function emoji(): string
    {
        return config("unicode_notifications.sources.{$this->value}.emoji", '🔔');
    }

    public static function values(): array
    {
        return array_map(fn ($c) => $c->value, self::cases());
    }
}
