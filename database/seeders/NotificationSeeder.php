<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('level_id', 1)->first();
        if (!$owner) return;

        $notifications = [
            ['title' => 'Anggota Baru', 'message' => 'Bima telah mendaftar sebagai anggota baru.', 'type' => 'success', 'icon' => 'fa-user-plus'],
            ['title' => 'Workshop Selesai', 'message' => 'Workshop Laravel telah selesai dengan sukses.', 'type' => 'info', 'icon' => 'fa-check-circle'],
            ['title' => 'Peringatan', 'message' => 'Ada pelanggaran ringan oleh salah satu anggota.', 'type' => 'warning', 'icon' => 'fa-exclamation-triangle'],
            ['title' => 'Poin Baru', 'message' => 'Nisa mendapatkan 50 poin.', 'type' => 'info', 'icon' => 'fa-star'],
        ];

        foreach ($notifications as $notif) {
            Notification::create([
                'user_id' => $owner->id,
                'type' => $notif['type'],
                'title' => $notif['title'],
                'message' => $notif['message'],
                'icon' => $notif['icon'],
                'link' => '#',
                'read_at' => rand(0, 1) ? now() : null,
                'created_at' => now()->subHours(rand(1, 24)),
            ]);
        }
    }
}