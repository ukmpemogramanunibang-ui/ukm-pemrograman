<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ambil semua notifikasi yang title-nya masih null
        $notifications = DB::table('notifications')
            ->whereNull('title')
            ->orWhere('title', '')
            ->get();

        foreach ($notifications as $n) {
            $data = is_string($n->data)
                ? json_decode($n->data, true) ?: []
                : (array) $n->data;

            // Tentukan source dari type
            $source = 'default';
            $type   = $n->type ?? '';
            if (str_contains($type, 'GroupAnnouncement')) {
                $source = 'group_announcement';
            } elseif (str_contains($type, 'Booking')) {
                $source = 'booking';
            } elseif (str_contains($type, 'Attendance')) {
                $source = 'attendance';
            } elseif (str_contains($type, 'Lms')) {
                $source = 'lms';
            } elseif (str_contains($type, 'Chat') || str_contains($type, 'Message')) {
                $source = 'chat';
            } elseif (str_contains($type, 'Financial')) {
                $source = 'financial';
            }

            DB::table('notifications')
                ->where('id', $n->id)
                ->update([
                    'title'       => $n->title ?: ($data['title'] ?? 'Notifikasi Baru'),
                    'message'     => $n->message ?: ($data['message'] ?? $data['body'] ?? ''),
                    'source'      => $n->source ?: $source,
                    'icon'        => $n->icon ?: 'bell',
                    'priority'    => $n->priority ?: 'normal',
                    'category'    => $n->category ?: 'general',
                    'updated_at'  => now(),
                ]);
        }
    }

    public function down(): void
    {
        // tidak reversible
    }
};