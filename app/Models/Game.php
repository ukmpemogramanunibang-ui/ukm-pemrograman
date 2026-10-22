<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory, SoftDeletes;

// app/Models/Game.php

protected $fillable = [
    'name', 'slug', 'type', 'source_type', 'game_type', 'description',
    'settings', 'url', 'integration_level', 'icon', 'banner',
    'developer', 'difficulty', 'max_attempts', 'status',
    'show_in_leaderboard', 'support_rating', 'support_matchmaking',
    'support_challenge', 'version', 'player_count', 'total_matches',
    'game_images', 'cover_images',
    'game_settings', 'typing_settings', 'quiz_settings', 'proctoring_settings',
    'quiz_questions', 'game_questions',
    'diagnostic_status', 'diagnostic_last_run',
];

protected $casts = [
    'game_settings' => 'array',
    'typing_settings' => 'array',
    'quiz_settings' => 'array',
    'proctoring_settings' => 'array',
    'quiz_questions' => 'array',
    'game_questions' => 'array',
    'game_images' => 'array',
    'cover_images' => 'array',
    'settings' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
];
public function sponsors()
{
    return $this->hasMany(\App\Models\GameSponsor::class);
}
    // ============================================================
    // RELASI
    // ============================================================
    public function sessions()
    {
        return $this->hasMany(GameSession::class);
    }

    // ============================================================
    // SCOPES
    // ============================================================
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeTyping($query)
    {
        return $query->where('game_type', 'typing');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================
    public function getCoverImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getSettingsAttribute()
    {
        return $this->game_settings ?? $this->typing_settings ?? $this->quiz_settings ?? [];
    }

    public function getQuestionsAttribute()
    {
        return $this->quiz_questions ?? $this->game_questions ?? [];
    }

    // ============================================================
    // HELPER
    // ============================================================
    public function isTyping(): bool
    {
        return $this->game_type === 'typing';
    }

    public function isQuiz(): bool
    {
        return in_array($this->game_type, ['quiz', 'tebak', 'logic', 'coding', 'puzzle']);
    }

    public function isExternal(): bool
    {
        return $this->source_type === 'external';
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}