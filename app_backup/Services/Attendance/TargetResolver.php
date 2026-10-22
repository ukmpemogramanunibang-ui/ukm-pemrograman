<?php

namespace App\Services\Attendance;

use App\Models\AttendanceSession;
use App\Models\AttendanceTarget;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

/**
 * Target Resolver
 * ===============
 * Mengubah "target_type + target_data" pada sesi absensi
 * menjadi daftar AttendanceTarget (auto-populate dari grup/role).
 */
class TargetResolver
{
    public function resolve(AttendanceSession $session): int
    {
        $type = $session->target_type;
        $data = $session->target_data ?? [];

        $query = User::query()->where('is_active', true);

        switch ($type) {
            case 'group':
                $groupIds = $data['group_ids'] ?? [];
                $query->whereHas('groups', fn($q) => $q->whereIn('groups.id', $groupIds));
                break;

            case 'role':
                $levels = $data['level_ids'] ?? [];
                $query->whereIn('level_id', $levels);
                break;

            case 'individual':
                $userIds = $data['user_ids'] ?? [];
                $query->whereIn('id', $userIds);
                break;

            case 'mixed':
                $query->where(function ($q) use ($data) {
                    if (!empty($data['group_ids'])) {
                        $q->orWhereHas('groups', fn($qq) => $qq->whereIn('groups.id', $data['group_ids']));
                    }
                    if (!empty($data['level_ids'])) {
                        $q->orWhereIn('level_id', $data['level_ids']);
                    }
                    if (!empty($data['user_ids'])) {
                        $q->orWhereIn('id', $data['user_ids']);
                    }
                });
                break;

            case 'all':
            default:
                // semua user aktif
                break;
        }

        $users = $query->get();

        // Exclude
        $excluded = $data['exclude_user_ids'] ?? [];

        DB::transaction(function () use ($session, $users, $excluded, $data) {
            // Hapus target lama (hanya yang belum punya record)
            AttendanceTarget::where('session_id', $session->id)
                ->whereDoesntHave('record')
                ->delete();

            foreach ($users as $user) {
                $target = AttendanceTarget::firstOrNew([
                    'session_id' => $session->id,
                    'user_id'    => $user->id,
                ]);

                $target->group_id = $user->groups->first()?->id;
                $target->division_id = $user->divisions->first()?->id;
                $target->is_excluded = in_array($user->id, $excluded);
                $target->exclude_reason = $target->is_excluded ? 'Dikecualikan oleh pengurus' : null;

                // Snapshot (immutable history)
                if (!$target->exists) {
                    $target->snapshot_name = $user->name;
                    $target->snapshot_nim = $user->nim;
                    $target->snapshot_role = $user->level->name ?? null;
                    $target->snapshot_group_name = $user->groups->first()?->name;
                    $target->snapshot_division_name = $user->divisions->first()?->name;
                }
                $target->target_mode = $this->resolveMode($user, $session, $data);
                $target->save();
            }

            $session->update([
                'target_count' => AttendanceTarget::where('session_id', $session->id)->where('is_excluded', false)->count(),
            ]);
        });

        return $session->target_count;
    }

    protected function resolveMode(User $user, AttendanceSession $session, array $data): string
    {
        if ($session->attendance_mode === 'online') return 'online';
        if ($session->attendance_mode === 'offline') return 'offline';

        // Hybrid: cek per user / per grup
        $onlineUsers = $data['online_user_ids'] ?? [];
        $onlineGroups = $data['online_group_ids'] ?? [];

        if (in_array($user->id, $onlineUsers)) return 'online';
        if ($user->groups->whereIn('id', $onlineGroups)->isNotEmpty()) return 'online';
        return 'offline';
    }
}