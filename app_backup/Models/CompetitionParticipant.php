<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'user_id', 'team_id', 'group_id',
        'seed', 'status', 'final_rank', 'eliminated_round',
        'total_score', 'total_xp'
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function matchesAsA()
    {
        return $this->hasMany(CompetitionMatch::class, 'participant_a_id');
    }

    public function matchesAsB()
    {
        return $this->hasMany(CompetitionMatch::class, 'participant_b_id');
    }
}