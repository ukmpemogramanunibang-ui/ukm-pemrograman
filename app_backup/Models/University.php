<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class University extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'universities';

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
        'university_uuid',
        'name',
        'short_name',
        'city',
        'province',
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
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'full_name',
        'location',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($university) {
            if (empty($university->university_uuid)) {
                $university->university_uuid = (string) Str::uuid();
            }
            if (is_null($university->is_active)) {
                $university->is_active = true;
            }
        });
    }

    /**
     * Get the majors for this university.
     */
    public function majors()
    {
        return $this->hasMany(Major::class, 'university_id');
    }

    /**
     * Get the users from this university.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'university_id');
    }

    /**
     * Get the active majors for this university.
     */
    public function activeMajors()
    {
        return $this->majors()->active();
    }

    /**
     * Scope a query to only include active universities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive universities.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to search by name or short_name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('short_name', 'LIKE', "%{$search}%")
              ->orWhere('city', 'LIKE', "%{$search}%")
              ->orWhere('province', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by province.
     */
    public function scopeByProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    /**
     * Scope a query to filter by city.
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', 'LIKE', "%{$city}%");
    }

    /**
     * Get the full name (with short name in parentheses).
     */
    public function getFullNameAttribute()
    {
        if ($this->short_name) {
            return "{$this->name} ({$this->short_name})";
        }
        return $this->name;
    }

    /**
     * Get the location (city, province).
     */
    public function getLocationAttribute()
    {
        $location = [];
        if ($this->city) {
            $location[] = $this->city;
        }
        if ($this->province) {
            $location[] = $this->province;
        }
        return implode(', ', $location) ?: '-';
    }

    /**
     * Get the display name for dropdown.
     */
    public function getDropdownLabelAttribute()
    {
        $label = $this->full_name;
        if ($this->city) {
            $label .= " - {$this->city}";
        }
        return $label;
    }

    /**
     * Check if university is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Activate the university.
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    /**
     * Deactivate the university.
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    /**
     * Get the total number of majors for this university.
     */
    public function getMajorsCountAttribute()
    {
        return $this->majors()->count();
    }

    /**
     * Get the total number of users from this university.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }
}