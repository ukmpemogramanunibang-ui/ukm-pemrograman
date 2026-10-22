<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_id', 'name', 'file_path', 'issued_at', 'qr_code', 'is_verified'];
    protected $casts = ['issued_at' => 'datetime', 'is_verified' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}