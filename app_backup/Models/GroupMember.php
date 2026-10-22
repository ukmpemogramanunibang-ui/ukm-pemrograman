<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = [
    'group_id',
    'user_id',
    'position',
    'status',
    'joined_at',
    'left_at',
    'can_view',
    'can_announcement',
    'can_message',
    'can_manage_members',
];

protected $casts = [
    'can_view' => 'boolean',
    'can_announcement' => 'boolean',
    'can_message' => 'boolean',
    'can_manage_members' => 'boolean',
    'joined_at' => 'datetime',
    'left_at' => 'datetime',
];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}