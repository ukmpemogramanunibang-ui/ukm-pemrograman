<?php

namespace App\Policies\Lms;

use App\Models\Lms\LmsGroup;
use App\Models\User;

class LmsGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4, 5]);
    }

    public function view(User $user, LmsGroup $group): bool
    {
        if (in_array($user->level_id, [1, 2])) return true;
        if ($group->creator_id === $user->id) return true;

        return $group->items()
            ->whereHas('recipients', fn ($q) => $q->where('user_id', $user->id))
            ->exists();
    }

    public function create(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4, 5]);
    }

    public function update(User $user, LmsGroup $group): bool
    {
        if (in_array($user->level_id, [1, 2])) return true;
        return $group->creator_id === $user->id;
    }

    public function delete(User $user, LmsGroup $group): bool
    {
        if (in_array($user->level_id, [1, 2])) return true;
        return $group->creator_id === $user->id;
    }

    public function restore(User $user, LmsGroup $group): bool
    {
        return in_array($user->level_id, [1, 2]);
    }

    public function forceDelete(User $user, LmsGroup $group): bool
    {
        return $user->level_id === 1;
    }
}
