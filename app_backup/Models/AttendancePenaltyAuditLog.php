<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePenaltyAuditLog extends Model
{
    protected $fillable = ['penalty_id','actor_id','action','description','before','after','meta'];
    protected $casts    = ['before' => 'array','after' => 'array','meta' => 'array'];

    public function penalty() { return $this->belongsTo(AttendancePenalty::class, 'penalty_id'); }
    public function actor()   { return $this->belongsTo(User::class, 'actor_id'); }
}