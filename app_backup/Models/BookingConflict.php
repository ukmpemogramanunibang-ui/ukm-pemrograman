<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingConflict extends Model
{
    protected $fillable = [
        'booking_id',
        'details',
        'resolved',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'details' => 'array',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}