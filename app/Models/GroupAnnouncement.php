<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 🔥 TAMBAHKAN INI

class GroupAnnouncement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'status',
        'is_important',
        'published_at',
    ];

    protected $casts = [
        'is_important' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relasi ke user pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke kelompok target (many-to-many via pivot)
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_announcement_targets', 'announcement_id', 'group_id')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }

    // Relasi ke pivot targets
    public function targets()
    {
        return $this->hasMany(GroupAnnouncementTarget::class);
    }
}