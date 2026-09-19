<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = ['room_id', 'title', 'description', 'start_datetime', 'end_datetime', 'status'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}