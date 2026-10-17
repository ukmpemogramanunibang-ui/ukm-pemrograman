<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;

class FeePlan extends Model
{
    protected $fillable = [
        // === IDENTITAS ===
        'generation_id',  // ✅ WAJIB ADA!
        'code', 'name', 'description', 'fee_type',
        // Target
        'target_type', 'target_data',
        // Nominal
        'amount', 'nominal_type', 'nominal_data',
        // Periode
        'frequency', 'period_type', 'due_day',
        // Jatuh tempo & toleransi
        'grace_period', 'non_active_day_policy', 'calendar_sources',
        // Denda
        'penalty_enabled', 'penalty_type', 'penalty_amount', 'penalty_escalation',
        // Cicilan
        'allow_partial', 'min_partial_amount', 'max_installments',
        // Pembebasan
        'allow_exemption', 'exemption_require_approval', 'exemption_reason_required',
        // Masa berlaku
        'effective_from', 'effective_until', 'start_period', 'end_period',
        // Membership rule
        'membership_rule',
        // Sanksi non-uang
        'sanction_rules',
        // Status & versioning
        'status', 'version', 'parent_id', 'superseded_at',
        // Audit
        'created_by', 'updated_by', 'approved_by', 'approved_at',
        // Catatan
        'notes'
    ];
    protected $casts = [
        'target_data' => 'array',
        'nominal_data' => 'array',
        'calendar_sources' => 'array',
        'penalty_escalation' => 'array',
        'membership_rule' => 'array',
        'sanction_rules' => 'array',
        'effective_from' => 'date',
        'effective_until' => 'date',
        'start_period' => 'date',
        'end_period' => 'date',
        'approved_at' => 'datetime',
        'superseded_at' => 'datetime',
        'penalty_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'min_partial_amount' => 'decimal:2',
    ];

    // ===== RELASI =====
    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }

public function invoices()
{
    return $this->hasMany(\App\Models\Financial\Invoice::class, 'fee_plan_id');
}

public function scopeCurrentlyActive($query)
{
    return $query->where('status', 'active')
        ->where(function ($q) {
            $q->whereNull('effective_from')->orWhere('effective_from', '<=', now());
        })
        ->where(function ($q) {
            $q->whereNull('effective_until')->orWhere('effective_until', '>=', now());
        });
}

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parent()
    {
        return $this->belongsTo(FeePlan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FeePlan::class, 'parent_id');
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', now());
            });
    }

    public function scopeByGeneration($query, $generationId)
    {
        return $query->where('generation_id', $generationId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('fee_type', $type);
    }

    public function scopeByPeriod($query, $date)
    {
        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', $date);
            });
    }

    // ===== ACCESSORS =====
    public function getFrequencyLabelAttribute(): string
    {
        $labels = [
            'monthly' => 'Bulanan',
            'biweekly' => '2 Minggu',
            'weekly' => 'Mingguan',
            'one_time' => 'Sekali Bayar',
        ];
        return $labels[$this->frequency] ?? $this->frequency;
    }

    public function getFeeTypeLabelAttribute(): string
    {
        $labels = [
            'monthly_cash' => 'Kas Bulanan',
            'registration' => 'Uang Pangkal',
            'special_contribution' => 'Iuran Khusus',
            'penalty' => 'Denda',
            'activity' => 'Kegiatan',
            'other' => 'Lainnya',
        ];
        return $labels[$this->fee_type] ?? $this->fee_type;
    }

    public function getTargetTypeLabelAttribute(): string
    {
        $labels = [
            'all_members' => 'Semua Anggota',
            'general_members' => 'Anggota Umum',
            'officials' => 'Pengurus',
            'core_officials' => 'Pengurus Inti',
            'group' => 'Kelompok Tertentu',
            'individual' => 'Individu Tertentu',
        ];
        return $labels[$this->target_type] ?? $this->target_type;
    }

    public function getNominalTypeLabelAttribute(): string
    {
        $labels = [
            'fixed' => 'Tetap',
            'by_position' => 'Berdasarkan Jabatan',
            'by_member' => 'Berdasarkan Anggota',
        ];
        return $labels[$this->nominal_type] ?? $this->nominal_type;
    }

    public function getPenaltyTypeLabelAttribute(): string
    {
        if (!$this->penalty_enabled) return 'Tidak Aktif';
        return $this->penalty_type === 'fixed' ? 'Nominal Tetap' : 'Persentase';
    }

    public function getNonActiveDayPolicyLabelAttribute(): string
    {
        $labels = [
            'fixed' => 'Tetap',
            'next_active' => 'Geser ke hari aktif berikutnya',
            'prev_active' => 'Geser ke hari aktif sebelumnya',
        ];
        return $labels[$this->non_active_day_policy] ?? $this->non_active_day_policy;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badge = match($this->status) {
            'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'inactive' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'archived' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
            'superseded' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            default => 'bg-gray-100 text-gray-800',
        };
        return $badge;
    }

    // ===== HELPERS =====
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->effective_from <= now()
            && ($this->effective_until === null || $this->effective_until >= now());
    }

    public function isSuperseded(): bool
    {
        return $this->status === 'superseded' || $this->superseded_at !== null;
    }

    public function getNominalForUser(User $user): float
    {
        if ($this->nominal_type === 'fixed') {
            return (float) $this->amount;
        }

        if ($this->nominal_type === 'by_position' && $this->nominal_data) {
            $position = $user->jabatans()->first();
            if ($position && isset($this->nominal_data[$position->id])) {
                return (float) $this->nominal_data[$position->id];
            }
        }

        if ($this->nominal_type === 'by_member' && $this->nominal_data) {
            if (isset($this->nominal_data[$user->id])) {
                return (float) $this->nominal_data[$user->id];
            }
        }

        return (float) $this->amount; // fallback
    }

    public function isTargetedToUser(User $user): bool
    {
        switch ($this->target_type) {
            case 'all_members':
                return true;
            case 'general_members':
                return $user->level_id === 5;
            case 'officials':
                return in_array($user->level_id, [3, 4]);
            case 'core_officials':
                return in_array($user->level_id, [1, 2, 3]);
            case 'group':
                if ($this->target_data && isset($this->target_data['group_ids'])) {
                    return $user->groups()->whereIn('group_id', $this->target_data['group_ids'])->exists();
                }
                return false;
            case 'individual':
                if ($this->target_data && isset($this->target_data['user_ids'])) {
                    return in_array($user->id, $this->target_data['user_ids']);
                }
                return false;
            default:
                return false;
        }
    }

    public function shouldGenerateInvoiceForPeriod(User $user, string $period): bool
    {
        // Cek apakah user aktif dalam periode tersebut
        if ($this->membership_rule) {
            if (isset($this->membership_rule['require_active_membership']) && $this->membership_rule['require_active_membership']) {
                if ($user->membership_status !== 'active') {
                    return false;
                }
            }

            // Cek aturan khusus untuk anggota baru
            if (isset($this->membership_rule['start_billing']) && $this->membership_rule['start_billing'] === 'next_month') {
                $joinDate = $user->created_at ?? $user->joined_at;
                if ($joinDate && $joinDate->format('Y-m') === $period) {
                    return false; // bulan bergabung tidak dikenakan tagihan
                }
            }

            // Cek aturan cut-off
            if (isset($this->membership_rule['cutoff_day']) && $this->membership_rule['cutoff_day']) {
                $joinDate = $user->created_at ?? $user->joined_at;
                if ($joinDate && $joinDate->format('Y-m') === $period) {
                    $day = (int) $joinDate->format('d');
                    if ($day > $this->membership_rule['cutoff_day']) {
                        return false; // masuk setelah cut-off, tagihan mulai bulan depan
                    }
                }
            }
        }

        // Cek apakah user masih aktif pada periode tersebut
        if ($user->membership_status === 'resigned' || $user->membership_status === 'blacklisted') {
            return false;
        }

        return true;
    }

    public function getDefaultCalendarSources(): array
    {
        return [
            'national_holidays' => true,
            'weekends' => true,
            'academic_calendar' => false,
            'ukm_activities' => false,
        ];
    }

    public function getDefaultSanctionRules(): array
    {
        return [
            [
                'level' => 1,
                'late_count' => 1,
                'actions' => ['warning'],
            ],
            [
                'level' => 2,
                'late_count' => 2,
                'actions' => ['warning', 'penalty'],
                'penalty_amount' => 1000,
            ],
            [
                'level' => 3,
                'late_count' => 3,
                'actions' => ['warning', 'penalty', 'task'],
                'penalty_amount' => 2500,
            ],
            [
                'level' => 4,
                'late_count' => 4,
                'actions' => ['warning', 'review', 'evaluation'],
            ],
        ];
    }

    // ===== BOOT =====
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = strtoupper(Str::random(8));
            }
            if (empty($model->version)) {
                $model->version = 1;
            }
            if (empty($model->calendar_sources)) {
                $model->calendar_sources = $model->getDefaultCalendarSources();
            }
            if (empty($model->sanction_rules)) {
                $model->sanction_rules = $model->getDefaultSanctionRules();
            }
        });

        // Log aktivitas (audit) bisa ditambahkan di sini
    }
}