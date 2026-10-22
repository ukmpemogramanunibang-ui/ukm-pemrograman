<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LmsNotificationService
{
    /**
     * Buat notifikasi sederhana (bisa dihubungkan ke sistem Notifikasi kamu).
     */
    public function notifyAssigned(LmsItem $item, array $userIds): void
    {
        $rows = [];
        $now  = now();
        foreach ($userIds as $uid) {
            $rows[] = [
                'user_id'    => $uid,
                'type'       => 'lms_task_assigned',
                'title'      => ($item->type === 'task' ? 'Tugas Baru: ' : 'Materi Baru: ') . $item->title,
                'body'       => 'Dari ' . ($item->creator->name ?? '-') .
                                ($item->deadline_at ? ' • Deadline: ' . $item->deadline_at->format('d M Y H:i') : ''),
                'data'       => json_encode(['item_id' => $item->id, 'url' => route('owner.lms.items.show', $item->id)]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('notifications')->insert($rows); // sesuaikan dengan tabel notifikasi kamu
        }
    }
}