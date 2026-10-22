<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'game_id', 'name', 'slug', 'description', 'type', 'participant_type',
        'status', 'max_participants', 'winner_count', 'is_live',
        'anti_cheat_enabled', 'auto_matchmaking', 'allow_admin_override',
        'starts_at', 'ends_at', 'created_by'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_live' => 'boolean',
        'anti_cheat_enabled' => 'boolean',
        'auto_matchmaking' => 'boolean',
        'allow_admin_override' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function participants()
    {
        return $this->hasMany(CompetitionParticipant::class);
    }

    public function rounds()
    {
        return $this->hasMany(CompetitionRound::class)->orderBy('round_number');
    }

    public function matches()
    {
        return $this->hasMany(CompetitionMatch::class);
    }

    public function sponsors()
    {
        return $this->hasMany(CompetitionSponsor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}