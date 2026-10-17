<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'parent_id',
        'name', 'title', 'description',
        'icon', 'url', 'open_in_new_tab',
        'audience_mode',
        'background_type', 'background_color', 'background_image',
        'category', 'priority', 'sort_order',
        'schedule_type', 'starts_at', 'ends_at',
        'is_active',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active'       => 'boolean',
        'starts_at'       => 'datetime',
        'ends_at'         => 'datetime',
    ];

    // =====================================================================
    // SCOPES
    // =====================================================================

    public function scopeActive($q)    { return $q->where('is_active', true); }
    public function scopeCards($q)     { return $q->where('type', 'card'); }
public function scopeOnlyShortcuts($q)  { return $q->where('type', 'shortcut'); }


    public function scopeLive($q)
    {
        $now = now();
        return $q->where(fn ($x) => $x->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                 ->where(fn ($x) => $x->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }

    public function scopeCurrentlyVisible($q)
    {
        $now = now();

        return $q->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->where('schedule_type', 'always')
                  ->orWhere(function ($q) use ($now) {
                      $q->whereIn('schedule_type', ['scheduled', 'expires'])
                        ->where(fn ($x) => $x->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
                        ->where(fn ($x) => $x->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
                  });
            });
    }

    // =====================================================================
    // RELATIONS
    // =====================================================================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

public function shortcuts(): HasMany
{
    return $this->hasMany(self::class, 'parent_id')
        ->where('type', 'shortcut')
        ->orderBy('sort_order')
        ->orderBy('name');
}

    public function activeShortcuts(): HasMany
    {
        return $this->shortcuts()->where('is_active', true);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(DashboardItemTarget::class);
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }

    // =====================================================================
    // HELPERS
    // =====================================================================

    public function isCard(): bool     { return $this->type === 'card'; }
    public function isShortcut(): bool { return $this->type === 'shortcut'; }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: $this->name;
    }

    public function isCurrentlyVisible(): bool
    {
        if (! $this->is_active) return false;
        if ($this->schedule_type === 'always') return true;

        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;

        return true;
    }

    public function isExpired(): bool
    {
        return $this->ends_at && now()->gt($this->ends_at);
    }

    public function getScheduleLabelAttribute(): string
    {
        return match ($this->schedule_type) {
            'scheduled' => 'Terjadwal',
            'expires'   => 'Kadaluarsa',
            default     => 'Selamanya',
        };
    }

    // =====================================================================
    // AUTO-CONTRAST
    // =====================================================================

    public function isLightBackground(): bool
    {
        if ($this->background_type === 'image') return false;
        if ($this->background_type === 'plain') return false;

        $hex = ltrim((string) $this->background_color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) return false;

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return ((0.299 * $r + 0.587 * $g + 0.114 * $b) / 255) > 0.6;
    }

    public function getTextColorAttribute(): string
    {
        return $this->isLightBackground() ? 'text-gray-900' : 'text-white';
    }

    public function getSubtextColorAttribute(): string
    {
        return $this->isLightBackground() ? 'text-gray-700' : 'text-white/85';
    }

    public function getIconBgAttribute(): string
    {
        return $this->isLightBackground() ? 'bg-black/10' : 'bg-white/20';
    }

    public function getBadgeBgAttribute(): string
    {
        return $this->isLightBackground() ? 'bg-black/10' : 'bg-white/20';
    }

    public function getBackgroundStyleAttribute(): string
    {
        if ($this->background_type === 'image' && $this->background_image) {
            return "background-image:linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),url('"
                . asset('storage/' . $this->background_image) . "');"
                . "background-size:cover;background-position:center;";
        }
        if ($this->background_type === 'color' && $this->background_color) {
            return "background:{$this->background_color};";
        }
        return 'background:linear-gradient(135deg,#0891b2,#2563eb,#4f46e5);';
    }
}