<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivePermission extends Model
{
    protected $fillable = [
        'archive_id', 'user_id', 'permission', 'shared_by'
    ];

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }
}