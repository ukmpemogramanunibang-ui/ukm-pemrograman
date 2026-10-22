<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePenaltyDispute extends Model
{
    protected $fillable = [
        'penalty_id','user_id','reason','status','reviewed_by','review_notes','reviewed_at','attachments',
    ];
    protected $casts = [
        'reviewed_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function penalty()  { return $this->belongsTo(AttendancePenalty::class, 'penalty_id'); }
    public function user()     { return $this->belongsTo(User::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}