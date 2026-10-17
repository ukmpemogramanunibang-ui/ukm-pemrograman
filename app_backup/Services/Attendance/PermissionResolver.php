<?php

namespace App\Services\Attendance;

use App\Models\AttendancePermission;

class PermissionResolver
{
    public function findApproved(int $userId, int $sessionId): ?AttendancePermission
    {
        return AttendancePermission::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('status', 'approved')
            ->latest('reviewed_at')
            ->first();
    }

    public function findPending(int $userId, int $sessionId): ?AttendancePermission
    {
        return AttendancePermission::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }
}