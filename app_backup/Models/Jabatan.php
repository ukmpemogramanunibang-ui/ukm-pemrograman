<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    // Relasi ke user (many-to-many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'jabatan_user')
                    ->withPivot(['is_primary', 'status', 'start_date', 'end_date'])
                    ->withTimestamps();
    }
}