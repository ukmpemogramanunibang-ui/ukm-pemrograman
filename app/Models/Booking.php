<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasLinkedNotifications;

class Booking extends Model
{
    use HasFactory, HasLinkedNotifications, SoftDeletes;
 protected $notificationIgnoredColumns = ['updated_at', 'view_count'];
protected $fillable = [
    'room_id',
    'user_id',
    'booking_code',       // ✅ dari migration
    'title',
    'description',
    'requester_type',     // ✅ dari migration
    'requester_id',       // ✅ dari migration
    'requester_name',
    'requester_phone',
    'participants',
    'source',             // ✅ dari migration
    'public_token',       // ✅ dipakai di storeBooking
    'start_datetime',
    'end_datetime',
    'status',
    'approved_by',
    'approved_at',
    'rejected_by',
    'rejected_at',
    'rejection_reason',
    'override_reason',
    'checkin_at',
    'checkout_at',
    'condition_report',
    'images',
    'documents',
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
public function toNotificationPayload(): array
{
    $requesterName = $this->user?->name
        ?? $this->requester_name
        ?? 'Pengguna';

    $roomName = $this->room?->nama
        ?? $this->room?->name
        ?? 'Ruangan';

    return [
        'title'           => '📅 Booking Ruangan ' . ($this->isDirty('status') ? '(Diperbarui)' : 'Baru'),
        'message'         => $requesterName . ' mengajukan booking ' . $roomName,
        'action_url'      => route('owner.bookings.show', $this->id),
        'source'          => 'booking',
        'priority'        => $this->status === 'urgent' ? 'urgent' : 'normal',
        'status'          => $this->status,
        'requires_action' => in_array($this->status, ['pending', 'review']),
    ];
}

    public function notificationRecipients(): array
    {
        return \App\Models\User::role(['owner', 'superadmin'])
            ->where('is_active', true)
            ->get()
            ->all();
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
/* ============================================================
   Accessor 'requester' — menggantikan relasi yang tidak ada
   ============================================================ */
public function getRequesterAttribute(): object
{
    return (object) [
        'type'  => $this->requester_type,
        'id'    => $this->requester_id,
        'name'  => $this->requester_name,
        'phone' => $this->requester_phone,
    ];
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