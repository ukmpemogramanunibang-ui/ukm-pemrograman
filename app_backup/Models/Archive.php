<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'file_name', 'file_id', 'mime_type',
        'google_drive_folder_id', 'uploaded_by', 'visibility', 'expires_at'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function permissions()
    {
        return $this->hasMany(ArchivePermission::class);
    }
}