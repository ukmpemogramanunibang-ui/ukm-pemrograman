<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupAnnouncement;
use App\Models\Notification as AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupAnnouncementService
{
    /**
     * Buat pengumuman + attach target + distribute notifikasi.
     */
    public function create(array $data, array $groupIds, bool $publish = true): array
    {
        return DB::transaction(function () use ($data, $groupIds, $publish) {

            $groups = Group::query()
                ->whereIn('id', $groupIds)
                ->where('status', 'aktif')
                ->get();

            if ($groups->isEmpty()) {
                throw new \RuntimeException('Tidak ada kelompok aktif yang valid di target.');
            }

            $announcement = GroupAnnouncement::create([
                'title'                   => $data['title'],
                'content'                 => $data['content'],
                'created_by'              => auth()->id(),
                'type'                    => $data['type'] ?? 'informasi',
                'priority'                => $data['priority'] ?? 'normal',
                'status'                  => $publish ? 'published' : ($data['status'] ?? 'draft'),
                'is_important'            => in_array($data['priority'] ?? 'normal', ['important', 'high', 'urgent'], true),
                'is_pinned'               => (bool) ($data['is_pinned'] ?? false),
                'require_acknowledgement' => (bool) ($data['require_acknowledgement'] ?? false),
                'scheduled_at'            => $data['scheduled_at'] ?? null,
                'published_at'            => $publish ? now() : null,
                'expires_at'              => $data['expires_at'] ?? null,
                'action_url'              => $data['action_url'] ?? null,
                'attachment_path'         => $data['attachment_path'] ?? null,
            ]);

            $announcement->groups()->sync($groups->pluck('id')->toArray());

            $result = [
                'announcement'       => $announcement,
                'recipient_count'    => 0,
                'notification_count' => 0,
                'skipped'            => 0,
            ];

            if ($publish) {
                $result = array_merge($result, $this->dispatchNotifications($announcement, $groups));
            }

            return $result;
        });
    }

    /**
     * Ambil anggota aktif dari semua target, dedupe, kirim notifikasi.
     * ✅ MANUAL INSERT — menggunakan model AppNotification custom.
     */
    public function dispatchNotifications(GroupAnnouncement $announcement, $groups = null): array
    {
        $groups = $groups ?? $announcement->groups;

        /* 1. Ambil user_id unik dari group_members */
        $userIds = DB::table('group_members')
            ->join('users', 'users.id', '=', 'group_members.user_id')
            ->whereIn('group_members.group_id', $groups->pluck('id'))
            ->where('group_members.status', 'aktif')
            ->whereNull('users.deleted_at')
            ->whereIn('users.level_id', [3, 4, 5])
            ->where('users.is_active', true)
            ->distinct()
            ->pluck('users.id')
            ->toArray();

        if (empty($userIds)) {
            $announcement->update(['recipient_count' => 0]);
            return ['recipient_count' => 0, 'notification_count' => 0, 'skipped' => 0];
        }

        /* 2. Ambil user objects */
        $users = User::whereIn('id', $userIds)->get();

        /* 3. Susun payload notifikasi */
        $groupNames = $announcement->groups->pluck('name')->take(3)->toArray();
        $groupCount = $announcement->groups->count();

        $payload = [
            'announcement_id' => $announcement->id,
            'title'           => $announcement->title,
            'message'         => \Illuminate\Support\Str::limit($announcement->content, 160),
            'type'            => $announcement->type,
            'priority'        => $announcement->priority,
            'group_names'     => $groupNames,
            'group_count'     => $groupCount,
            'published_at'    => optional($announcement->published_at)->toIso8601String(),
            'expires_at'      => optional($announcement->expires_at)->toIso8601String(),
            'require_ack'     => $announcement->require_acknowledgement,
        ];

        /* 4. Insert notifikasi per user (custom model) */
        $now = now();
        $inserted = 0;

        foreach ($users as $user) {
            try {
                AppNotification::create([
                    'user_id'         => $user->id,
                    'type'            => 'App\\Notifications\\GroupAnnouncementPublished',
                    'title'           => $announcement->title,
                    'message'         => \Illuminate\Support\Str::limit($announcement->content, 160),
                    'icon'            => 'bullhorn',
                    'source'          => 'group_announcement',
                    'type_slug'       => 'group_announcement.' . ($announcement->type ?? 'informasi'),
                    'category'        => 'announcement',
                    'priority'        => $announcement->priority ?? 'normal',
                    'data'            => $payload,
                    'action_url'      => null, // diisi setelah notifikasi dibuat
                    'requires_action' => (bool) $announcement->require_acknowledgement,
                    'entity_type'     => GroupAnnouncement::class,
                    'entity_id'       => $announcement->id,
                    'read_at'         => null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);

                $inserted++;
            } catch (\Throwable $e) {
                Log::warning('Gagal insert notifikasi pengumuman', [
                    'user_id'         => $user->id,
                    'announcement_id' => $announcement->id,
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        /* 5. Set action_url ke masing-masing notifikasi (butuh ID notifikasi) */
        AppNotification::where('user_id', '!=', null)
            ->where('entity_type', GroupAnnouncement::class)
            ->where('entity_id', $announcement->id)
            ->update([
                'action_url' => route('owner.notifications.show', ['notification' => '__ID__']),
            ]);

        $announcement->update(['recipient_count' => $inserted]);

        Log::info('Group announcement published', [
            'announcement_id' => $announcement->id,
            'group_count'     => $groups->count(),
            'recipient_count' => $inserted,
            'by'              => auth()->id(),
        ]);

        return [
            'recipient_count'    => $inserted,
            'notification_count' => $inserted,
            'skipped'            => count($userIds) - $inserted,
        ];
    }
}