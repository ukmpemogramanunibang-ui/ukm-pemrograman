<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Financial\GenerationMembership;
use Illuminate\Support\Str;

class Generation extends Model
{
    protected $fillable = [
        'code', 'name', 'slug', 'description',
        'entry_year', 'start_date', 'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'entry_year' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===== RELASI =====
    public function users()
    {
        return $this->hasMany(User::class, 'generation_id');
    }

    public function memberships()
    {
        return $this->hasMany(GenerationMembership::class);
    }

    public function activeMemberships()
    {
        return $this->memberships()->where('status', 'active');
    }

    public function feePlans()
    {
        return $this->hasMany(FeePlan::class);
    }

    // ===== ACCESSORS =====
    public function getTotalMembersAttribute()
    {
        return $this->activeMemberships()->count();
    }

    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) {
            return '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Nonaktif</span>';
        }

        $now = now();
        if ($this->start_date && $this->end_date) {
            if ($now->between($this->start_date, $this->end_date)) {
                return '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>';
            } elseif ($now->greaterThan($this->end_date)) {
                return '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Selesai</span>';
            }
        }
        return '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Akan Datang</span>';
    }

    public function getPeriodLabelAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('Y') . ' – ' . $this->end_date->format('Y');
        }
        return $this->entry_year;
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('entry_year', $year);
    }

    // ===== HELPER =====
    public function isEligibleUser(User $user): bool
    {
        $allowedRoles = [3, 4, 5]; // Ketua, Pengurus, Anggota UKM
        return in_array($user->level_id, $allowedRoles);
    }

    public function addUser(User $user, $startDate = null, $reason = null, $assignedBy = null)
    {
        // Cek apakah user sudah memiliki membership aktif di generasi ini
        $existing = $this->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return false;
        }

        // Tutup membership aktif user di generasi lain
        GenerationMembership::where('user_id', $user->id)
            ->where('status', 'active')
            ->update([
                'status' => 'completed',
                'ended_at' => now(),
                'reason' => 'Pindah ke generasi lain'
            ]);

        // Buat membership baru
        return $this->memberships()->create([
            'user_id' => $user->id,
            'started_at' => $startDate ?? now(),
            'status' => 'active',
            'reason' => $reason ?? 'Penambahan anggota',
            'assigned_by' => $assignedBy ?? auth()->id(),
        ]);
    }

    public function removeUser(User $user, $reason = null)
    {
        $membership = $this->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$membership) {
            return false;
        }

        $membership->update([
            'status' => 'removed',
            'ended_at' => now(),
            'reason' => $reason ?? 'Dikeluarkan dari generasi',
        ]);

        return true;
    }

    public function completeUser(User $user, $reason = null)
    {
        $membership = $this->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$membership) {
            return false;
        }

        $membership->update([
            'status' => 'completed',
            'ended_at' => now(),
            'reason' => $reason ?? 'Masa keanggotaan selesai',
        ]);

        return true;
    }

    public function transferUser(User $user, Generation $targetGeneration, $reason = null)
    {
        // Selesaikan membership di generasi saat ini
        $this->completeUser($user, $reason ?? 'Dipindahkan ke ' . $targetGeneration->name);

        // Tambahkan ke generasi tujuan
        return $targetGeneration->addUser($user, now(), $reason ?? 'Dipindahkan dari ' . $this->name);
    }

    public function getMembersByRole()
    {
        return $this->activeMemberships()
            ->with('user')
            ->get()
            ->groupBy('user.level_id')
            ->map(function ($group) {
                return $group->count();
            });
    }

    // ===== BOOT =====
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $last = self::orderBy('id', 'desc')->first();
                $number = $last ? intval(substr($last->code, -3)) + 1 : 1;
                $model->code = 'GEN-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name . '-' . ($model->entry_year ?? date('Y')));
            }
        });
    }
}