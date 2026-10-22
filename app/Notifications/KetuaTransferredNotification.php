<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class KetuaTransferredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $fromName,
        public string $comment
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type'    => 'ketua_transferred',
            'title'   => '🎉 Anda Resmi Menjadi Ketua',
            'message' => "Jabatan Ketua ditransfer dari {$this->fromName}. Pesan: {$this->comment}",
            'action'  => 'transfer_ketua',
            'icon'    => 'fa-crown',
            'color'   => 'amber',
        ];
    }
}