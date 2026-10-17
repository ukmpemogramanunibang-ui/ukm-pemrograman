<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ambil semua notifikasi rusak
        $broken = DB::table('notifications')
            ->where(function ($q) {
                $q->whereNull('message')->orWhere('message', '');
            })
            ->orWhere(function ($q) {
                $q->whereNull('title')->orWhere('title', '');
            })
            ->orWhere('title', 'Notifikasi Baru')
            ->get();

        $fixed = 0;

        foreach ($broken as $n) {
            $data = is_string($n->data)
                ? json_decode($n->data, true) ?: []
                : (array) $n->data;

            // Fix title
            $title = $n->title;
            if (! $title || $title === 'Notifikasi Baru' || $title === 'Notifikasi') {
                $title = $data['title']
                    ?? $data['subject']
                    ?? (isset($data['conversation_id']) ? 'Pesan Chat Baru' : null)
                    ?? (isset($data['announcement_id']) ? 'Pengumuman Baru' : null)
                    ?? (isset($data['booking_id']) ? 'Booking Ruangan Baru' : null)
                    ?? 'Notifikasi';
            }

            // Fix message
            $message = $n->message;
            if (! $message) {
                $message = $data['message']
                    ?? $data['message_preview']
                    ?? $data['body']
                    ?? $data['content']
                    ?? $data['description']
                    ?? '';
            }

            // Fix source
            $source = $n->source;
            if (! $source || $source === 'default') {
                if (isset($data['conversation_id'])) $source = 'chat';
                elseif (isset($data['announcement_id'])) $source = 'group_announcement';
                elseif (isset($data['booking_id'])) $source = 'booking';
                elseif (isset($data['attendance_id'])) $source = 'attendance';
                elseif (isset($data['lms_item_id'])) $source = 'lms';
                else $source = 'system';
            }

            DB::table('notifications')
                ->where('id', $n->id)
                ->update([
                    'title'   => $title,
                    'message' => $message,
                    'source'  => $source,
                ]);

            $fixed++;
        }

        echo "Fixed {$fixed} notifikasi rusak.\n";
    }

    public function down(): void {}
};