<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'user_id',
        'title',
        'description',
        'requester_name',
        'requester_phone',
        'start_datetime',
        'end_datetime',
        'participants',
        'status', // pending, conflict, approved, rejected, cancelled, completed, expired
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'override_reason',
        'checkin_at',
        'checkout_at',
        'condition_report',
        'images', // json
        'documents', // json
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
        'images' => 'array',
        'documents' => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function facilities()
{
    return $this->belongsToMany(RoomFacility::class, 'booking_facilities')->withPivot('quantity');
}

    public function conflicts()
    {
        return $this->hasMany(BookingConflict::class);
    }

    public function schedule()
{
    return $this->hasOne(RoomSchedule::class);
}

    // Scope untuk booking yang sedang aktif
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'conflict', 'approved']);
    }

    // Cek apakah booking bertabrakan dengan yang lain
    public static function checkOverlap($roomId, $start, $end, $exceptId = null)
    {
        $query = self::where('room_id', $roomId)
            ->where('status', 'approved')
            ->where(function($q) use ($start, $end) {
                $q->where('start_datetime', '<', $end)
                  ->where('end_datetime', '>', $start);
            });
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        return $query->exists();
    }
}