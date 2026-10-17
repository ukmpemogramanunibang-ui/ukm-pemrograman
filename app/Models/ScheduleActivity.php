<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleActivity extends Model
{
    use HasFactory;

    protected $table = 'schedule_activities';

    protected $fillable = [
        'schedule_id',
        'day',            // monday..sunday
        'stage',          // 1,2,3,...
        'stage_name',     // "Pembukaan", dst
        'activity_type',  // Kuliah, Rapat, dst
        'start_time',     // HH:MM
        'end_time',       // HH:MM
        'start_datetime', // full datetime
        'end_datetime',
        'person',
        'generation_ids',
        'kelompok_ids',
        'description',
    ];

    protected $casts = [
        'generation_ids' => 'array',
        'kelompok_ids'   => 'array',
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(RoomSchedule::class, 'schedule_id');
    }
}