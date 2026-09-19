<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = ['user_id', 'email_or_nim', 'ip_address', 'success', 'attempted_at'];
    public $timestamps = false; // kita sudah pakai attempted_at
}