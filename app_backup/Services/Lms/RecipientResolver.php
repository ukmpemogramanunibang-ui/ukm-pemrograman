<?php

namespace App\Services\Lms;

use App\Models\Lms\LmsItem;
use App\Models\Lms\LmsItemUser;
use App\Models\User;

class RecipientResolver
{
    /**
     * Resolve daftar user_id dari role & user_ids.
     */
    public function resolve(LmsItem $item, array $roleIds = [], array $userIds = []): array
    {
        $ids = collect();

        $mode = $item->target_mode ?: 'role';

        if (in_array($mode, ['role', 'both'], true) && ! empty($roleIds)) {
            $ids = $ids->merge(
                User::query()
                    ->whereIn('level_id', $roleIds)
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                    ->pluck('id')
            );
        }

        if (in_array($mode, ['individual', 'both'], true) && ! empty($userIds)) {
            $ids = $ids->merge($userIds);
        }

        return $ids->unique()->values()->all();
    }

    /**
     * Snapshot penerima ke lms_item_users.
     * Menghapus snapshot lama kalau ada.
     */
    public function assign(LmsItem $item, array $userIds, string $mode = 'role'): void
    {
        LmsItemUser::where('item_id', $item->id)->delete();

        $now = now();

        $rows = collect($userIds)->unique()->map(fn ($uid) => [
            'item_id'     => $item->id,
            'user_id'     => $uid,
            'status'      => 'new',
            'assigned_at' => $now,
            'created_at'  => $now,
            'updated_at'  => $now,
        ])->all();

        if (! empty($rows)) {
            LmsItemUser::insert($rows);
        }
    }
}