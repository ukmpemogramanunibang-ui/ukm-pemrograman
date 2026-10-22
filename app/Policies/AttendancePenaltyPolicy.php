<?php

namespace App\Policies;

use App\Models\AttendancePenalty;
use App\Models\User;

class AttendancePenaltyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4]); // owner, superadmin, ketua, pengurus
    }

    public function view(User $user, AttendancePenalty $p): bool
    {
        return $this->viewAny($user) || $p->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3]);
    }

    public function update(User $user, AttendancePenalty $p): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4]);
    }

    public function approve(User $user, AttendancePenalty $p): bool
    {
        return in_array($user->level_id, [1, 2, 3]);
    }

    public function cancel(User $user, AttendancePenalty $p): bool
    {
        return in_array($user->level_id, [1, 2, 3]);
    }

    public function delete(User $user, AttendancePenalty $p): bool
    {
        return in_array($user->level_id, [1, 2]);
    }
}