<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Major extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'majors';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'id';

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'major_uuid',
        'university_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        // Tidak ada yang perlu disembunyikan
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'university_id' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($major) {
            if (empty($major->major_uuid)) {
                $major->major_uuid = (string) Str::uuid();
            }
            if (is_null($major->is_active)) {
                $major->is_active = true;
            }
        });
    }

    /**
     * Get the university that owns the major.
     */
    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    /**
     * Get the users with this major.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'major_id');
    }

    /**
     * Scope a query to only include active majors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive majors.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to search by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by university.
     */
    public function scopeByUniversity($query, $universityId)
    {
        return $query->where('university_id', $universityId);
    }

    /**
     * Get the display name with code.
     */
    public function getDisplayNameAttribute()
    {
        if ($this->code) {
            return "{$this->code} - {$this->name}";
        }
        return $this->name;
    }

    /**
     * Get the display name for dropdown.
     */
    public function getDropdownLabelAttribute()
    {
        $label = $this->name;
        if ($this->code) {
            $label = "{$this->code} - {$label}";
        }
        if ($this->university) {
            $label .= " ({$this->university->short_name})";
        }
        return $label;
    }

    /**
     * Check if major is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Activate the major.
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    /**
     * Deactivate the major.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }
}