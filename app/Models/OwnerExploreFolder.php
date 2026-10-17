<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnerExploreFolder extends Model
{
    protected $fillable = ['owner_id', 'name', 'description', 'color'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(OwnerExploreCard::class, 'folder_id')
            ->with('user')
            ->latest();
    }

    public function getColorClassesAttribute(): array
    {
        return match ($this->color) {
            'amber'   => ['bg' => 'bg-amber-100 dark:bg-amber-900/30',   'text' => 'text-amber-600 dark:text-amber-300'],
            'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-300'],
            'rose'    => ['bg' => 'bg-rose-100 dark:bg-rose-900/30',     'text' => 'text-rose-600 dark:text-rose-300'],
            'cyan'    => ['bg' => 'bg-cyan-100 dark:bg-cyan-900/30',     'text' => 'text-cyan-600 dark:text-cyan-300'],
            'purple'  => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-300'],
            default   => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'text' => 'text-indigo-600 dark:text-indigo-300'],
        };
    }
}