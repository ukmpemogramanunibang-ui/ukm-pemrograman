<?php

namespace App\Policies\Lms;

use App\Models\Lms\LmsItem;
use App\Models\User;

class LmsItemPolicy
{
    public function view(User $user, LmsItem $item): bool
    {
        if (in_array($user->level_id, [1, 2])) return true;
        if ($item->created_by === $user->id) return true;

        return $item->recipients()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4, 5]);
    }

public function update(User $user, LmsItem $item): bool
{
    return (int) $user->id === (int) $item->created_by;
}

public function delete(User $user, LmsItem $item): bool
{
    return (int) $user->id === (int) $item->created_by;
}
    public function review(User $user, LmsItem $item): bool
    {
        if (in_array($user->level_id, [1, 2, 3])) return true;
        return $item->created_by === $user->id;
    }
}
