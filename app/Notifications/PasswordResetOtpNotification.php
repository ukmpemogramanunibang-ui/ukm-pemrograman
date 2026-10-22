<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $otp,
        public string $userName = 'Pengguna'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(
                config('mail.from.address'),
                config('mail.from.name')
            )
            ->subject('🔐 Kode OTP Pemulihan Akun – UNICODE')
            ->greeting('UKM Pemrograman UNICODE')
            ->line("Halo {$this->userName},")
            ->line('Kami menerima permintaan pemulihan akun UNICODE Anda.')
            ->line('Kode OTP Anda:')
            ->line("### {$this->otp}")
            ->line('Kode berlaku selama **5 menit**.')
            ->line('⚠️ Jangan berikan kode ini kepada siapa pun, termasuk pengurus.')
            ->line('Jika Anda tidak melakukan permintaan ini, abaikan email ini.')
            ->salutation("Salam,\nUKM Pemrograman UNICODE\nUniversitas Bhakti Asih – Tangerang");
    }
}