<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['code', 'name', 'description', 'photo', 'status', 'created_by', 'max_members'];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function announcements()
{
    return $this->belongsToMany(GroupAnnouncement::class, 'group_announcement_targets')
        ->withPivot('is_read', 'read_at')
        ->withTimestamps();
}
}