<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoleChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $action,
        public string $actorName,
        public ?string $comment = null
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $titles = [
            'promote'         => 'Pangkat Anda Dinaikkan',
            'demote'          => 'Pangkat Anda Diturunkan',
            'set_level'       => 'Level Akun Anda Diubah',
            'set_ketua'       => 'Anda Menjadi Ketua',
            'add_position'    => 'Jabatan Baru Ditambahkan',
            'remove_position' => 'Jabatan Anda Dikurangi',
        ];

        return [
            'type'    => 'role_changed',
            'title'   => $titles[$this->action] ?? 'Perubahan Role',
            'message' => "Oleh: {$this->actorName}" . ($this->comment ? " — {$this->comment}" : ''),
            'action'  => $this->action,
            'icon'    => 'fa-user-shield',
            'color'   => 'purple',
        ];
    }
}