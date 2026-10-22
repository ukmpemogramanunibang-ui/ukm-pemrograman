<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id', 'round_id', 'match_number',
        'participant_a_id', 'participant_b_id',
        'winner_id', 'loser_id',
        'score_a', 'score_b',
        'status', 'game_session_id',
        'started_at', 'finished_at'
    ];

    protected $casts = [
        'score_a' => 'decimal:2',
        'score_b' => 'decimal:2',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function round()
    {
        return $this->belongsTo(CompetitionRound::class);
    }

    public function participantA()
    {
        return $this->belongsTo(CompetitionParticipant::class, 'participant_a_id');
    }

    public function participantB()
    {
        return $this->belongsTo(CompetitionParticipant::class, 'participant_b_id');
    }

    public function winner()
    {
        return $this->belongsTo(CompetitionParticipant::class, 'winner_id');
    }

    public function loser()
    {
        return $this->belongsTo(CompetitionParticipant::class, 'loser_id');
    }

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'upcoming' => 'gray',
            'active' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }
}