<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'published_at',
        'created_by',
        // tambahkan field lain sesuai kebutuhan
    ];

    // Relasi ke user pembuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi many-to-many ke user yang menerima pengumuman
    public function users()
    {
        return $this->belongsToMany(User::class, 'announcement_user')
                    ->withPivot('created_at')
                    ->withTimestamps();
    }
}