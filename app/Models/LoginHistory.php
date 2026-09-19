<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;

class LoginHistory extends Model
{
    /** @use HasFactory<\Database\Factories\LoginHistoryFactory> */
    use HasFactory;
    
}
Notification::create([
    'user_id' => $receiver->id,  // user yang akan menerima
    'type'    => 'info',
    'title'   => 'Pesan Chat Baru',
    'message' => $sender->name . ' mengirim pesan kepada Anda.',
    'icon'    => 'fas fa-comments',
    'link'    => route('owner.chats.private', $sender->user_uuid),
    'data'    => json_encode(['sender_id' => $sender->id]),
]);

// Contoh: setelah pengumuman dibuat (broadcast ke semua user aktif)
$users = User::where('is_active', true)->get();
foreach ($users as $user) {
    Notification::create([
        'user_id' => $user->id,
        'type'    => 'success',
        'title'   => 'Pengumuman Baru',
        'message' => $announcement->judul,
        'icon'    => 'fas fa-bullhorn',
        'link'    => route('owner.announcements.show', $announcement->id),
    ]);
}