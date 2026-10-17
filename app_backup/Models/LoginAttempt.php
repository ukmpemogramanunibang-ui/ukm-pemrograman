<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'email_or_nim',
        'ip_address',
        'success',
        'attempted_at',
        'note',           // ← TAMBAHKAN INI
    ];

    public $timestamps = false;

    protected $casts = [
        'success'      => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}