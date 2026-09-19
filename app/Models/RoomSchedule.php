<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id', 'title', 'description', 'start_datetime', 'end_datetime',
        'activity_type', 'status', 'booking_id', 'recurrence_type',
        'recurrence_days', 'semester', 'academic_year', 'images', 'documents'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'recurrence_days' => 'array',
        'images' => 'array',
        'documents' => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}