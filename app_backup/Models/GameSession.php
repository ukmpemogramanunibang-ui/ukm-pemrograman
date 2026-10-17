<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id', 'game_id', 'score', 'metrics', 'mode',
        'result', 'started_at', 'ended_at'
    ];

    protected $casts = [
        'metrics' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}