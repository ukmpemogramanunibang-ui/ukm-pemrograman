<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendancePenaltyRule extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_penalty_rules';

    protected $fillable = [
        'code','name','description','violation_type','penalty_type',
        'min_occurrences','max_occurrences','period_scope',
        'point_penalty','financial_amount','currency','warning_level','severity',
        'requires_review','requires_approval','auto_generate',
        'due_days','escalation_actions','is_active','priority',
    ];

    protected $casts = [
        'escalation_actions' => 'array',
        'is_active'          => 'boolean',
        'requires_review'    => 'boolean',
        'requires_approval'  => 'boolean',
        'auto_generate'      => 'boolean',
        'financial_amount'   => 'decimal:2',
        'point_penalty'      => 'integer',
        'warning_level'      => 'integer',
        'priority'           => 'integer',
        'due_days'           => 'integer',
    ];

    // ================= SCOPES =================
    public function scopeActive(Builder $q): Builder { return $q->where('is_active', true); }
    public function scopeForViolation(Builder $q, string $type): Builder { return $q->where('violation_type', $type); }

    // ================= ACCESSORS =================
    public function getSeverityColorAttribute(): string {
        return match ($this->severity) {
            'low'      => 'emerald',
            'medium'   => 'amber',
            'high'     => 'orange',
            'critical' => 'red',
            default    => 'slate',
        };
    }

    public function getEscalationLabelsAttribute(): array {
        return collect($this->escalation_actions ?? [])
            ->map(fn($a) => match ($a) {
                'send_warning'          => 'Kirim peringatan',
                'notify_leader'         => 'Notifikasi ketua',
                'notify_owner'          => 'Notifikasi owner',
                'recommend_suspension'  => 'Rekomendasi suspensi',
                default                 => str_replace('_', ' ', $a),
            })
            ->all();
    }

    // ================= HELPERS =================
    public function matchesOccurrences(int $count): bool
    {
        if ($count < $this->min_occurrences) return false;
        if ($this->max_occurrences !== null && $count > $this->max_occurrences) return false;
        return true;
    }
}