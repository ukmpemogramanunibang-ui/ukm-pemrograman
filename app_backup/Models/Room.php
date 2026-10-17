<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'room_code', 'room_token', 'name', 'category', 'description',
        'building', 'floor', 'address',
        'capacity_normal', 'capacity_max',
        'condition', 'status',
        'responsible_person', 'responsible_phone',
        'operational_hours', 'last_used_at',
        'has_qr', 'qr_code', 'images', 'documents',
        'public_status', 'published_at', 'public_roles', 'public_fields'
    ];

    protected $casts = [
        'operational_hours' => 'array',
        'images' => 'array',
        'documents' => 'array',
        'last_used_at' => 'datetime',
        'has_qr' => 'boolean',
        'public_roles' => 'array',
    'public_fields' => 'array',
    'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($room) {
            if (empty($room->uuid)) $room->uuid = (string) Str::uuid();
            if (empty($room->room_token)) $room->room_token = Str::random(64);
            if (empty($room->room_code)) {
                $room->room_code = self::generateRoomCode($room);
            }
        });
    }
public function maintenances()
{
    return $this->hasMany(Maintenance::class);
}
    public static function generateRoomCode($room): string
    {
        $category = $room->category ?? 'UM';
        $categoryCode = strtoupper(substr($category, 0, 2));
        if (strlen($categoryCode) < 2) $categoryCode = 'RU';

        $floor = $room->floor ?? '0';
        preg_match('/(\d+)/', $floor, $matches);
        $floorNum = $matches[1] ?? '0';
        if (strpos(strtolower($floor), 'basement') !== false) {
            $floorNum = 'B' . $floorNum;
        }

        $capacity = $room->capacity_normal ?? 0;
        $capStr = str_pad($capacity % 100, 2, '0', STR_PAD_LEFT);

        $lastRoom = Room::withTrashed()->orderBy('id', 'desc')->first();
        $seq = $lastRoom ? str_pad($lastRoom->id + 1, 3, '0', STR_PAD_LEFT) : '001';

        return "{$categoryCode}-{$floorNum}-{$capStr}-{$seq}";
    }

    // ===================== RELATIONS =====================
    public function facilities()
    {
        return $this->hasMany(RoomFacility::class);
    }

    public function schedules()
    {
        return $this->hasMany(RoomSchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ===================== LOGIKA OPERASIONAL =====================
    public function isCurrentlyOpen(): bool
    {
        $hours = $this->operational_hours;
        if (empty($hours) || !is_array($hours)) return true;

        $now = now();
        $dayName = strtolower($now->isoFormat('dddd'));
        if (!isset($hours[$dayName])) return true;

        $range = $hours[$dayName];
        if (empty($range) || $range === 'Tutup' || $range === 'Closed') return false;

        if (!str_contains($range, '-')) return true;
        [$start, $end] = explode('-', $range);
        $currentTime = $now->format('H:i');
        return $currentTime >= $start && $currentTime <= $end;
    }

    public function isCurrentlyInUse(): bool
    {
        return $this->bookings()
            ->where('status', 'Disetujui')
            ->where('start_datetime', '<=', now())
            ->where('end_datetime', '>=', now())
            ->exists();
    }

    public function isOvertime(): bool
    {
        return !$this->isCurrentlyOpen() && $this->isCurrentlyInUse();
    }

    // ===================== SCOPES =====================
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'Dinonaktifkan')
                     ->where('status', '!=', 'Diarsipkan');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'Tersedia');
    }
}