<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3, 4], true);
    }

    public function view(User $user, AuditLog $log): bool
    {
        return $this->viewAny($user);
    }

    public function export(User $user): bool
    {
        return in_array($user->level_id, [1, 2, 3], true);
    }

    public function verify(User $user): bool
    {
        return in_array($user->level_id, [1, 2], true);
    }
}