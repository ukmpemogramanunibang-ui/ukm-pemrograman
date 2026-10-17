<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupAnnouncementTarget extends Model
{
    protected $fillable = [
        'announcement_id',
        'group_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

public function announcement()
{
    return $this->belongsTo(GroupAnnouncement::class, 'announcement_id');
}

public function group()
{
    return $this->belongsTo(Group::class, 'group_id');
}
}