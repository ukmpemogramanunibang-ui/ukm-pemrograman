<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;

    protected $table = 'game_matches';

    protected $fillable = [
        'game_id', 'player1_id', 'player2_id', 'status',
        'winner_id', 'score_player1', 'score_player2',
        'started_at', 'ended_at', 'match_data'
    ];

    protected $casts = [
        'match_data' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player1()
    {
        return $this->belongsTo(Player::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Player::class, 'winner_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'active']);
    }
}