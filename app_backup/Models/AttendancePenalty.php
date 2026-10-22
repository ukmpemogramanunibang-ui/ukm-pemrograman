<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendancePenalty extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_penalties';

    protected $fillable = [
        'case_number','user_id','group_id','attendance_session_id','attendance_record_id',
        'rule_id','penalty_code','penalty_type','violation_type','severity',
        'points','amount','amount_paid','currency','warning_level',
        'status','payment_status','reason','notes',
        'issued_at','due_at','paid_at','resolved_at',
        'created_by','approved_by','approved_at',
        'cancelled_by','cancelled_at','cancellation_reason','meta',
    ];

    protected $casts = [
        'issued_at'    => 'datetime',
        'due_at'       => 'datetime',
        'paid_at'      => 'datetime',
        'resolved_at'  => 'datetime',
        'approved_at'  => 'datetime',
        'cancelled_at' => 'datetime',
        'meta'         => 'array',
        'amount'       => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'points'       => 'integer',
        'warning_level'=> 'integer',
    ];

    // ================= RELASI =================
    public function user()      { return $this->belongsTo(User::class); }
    public function group()     { return $this->belongsTo(Group::class); }
    public function session()   { return $this->belongsTo(AttendanceSession::class, 'attendance_session_id'); }
    public function record()    { return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id'); }
    public function rule()      { return $this->belongsTo(AttendancePenaltyRule::class, 'rule_id'); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function approver()  { return $this->belongsTo(User::class, 'approved_by'); }
    public function canceller() { return $this->belongsTo(User::class, 'cancelled_by'); }

    public function disputes()  { return $this->hasMany(AttendancePenaltyDispute::class, 'penalty_id'); }
    public function audits()    { return $this->hasMany(AttendancePenaltyAuditLog::class, 'penalty_id')->orderByDesc('created_at'); }

    // ================= SCOPES =================
    public function scopeActive(Builder $q): Builder {
        return $q->whereIn('status', ['active','partial','overdue','disputed']);
    }
    public function scopeUnpaid(Builder $q): Builder {
        return $q->whereIn('payment_status', ['unpaid','partial']);
    }
    public function scopeOverdue(Builder $q): Builder {
        return $q->where('status', 'overdue');
    }
    public function scopeFinancial(Builder $q): Builder {
        return $q->where('amount', '>', 0);
    }
    public function scopeResolved(Builder $q): Builder {
        return $q->whereIn('status', ['paid','waived','reversed','cancelled']);
    }
    public function scopeForPeriod(Builder $q, $from, $to): Builder {
        return $q->whereBetween('created_at', [$from, $to]);
    }
    public function scopeSearch(Builder $q, ?string $term): Builder {
        if (!$term) return $q;
        return $q->where(function ($qq) use ($term) {
            $qq->where('case_number', 'like', "%{$term}%")
               ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%")
                                                  ->orWhere('nim', 'like', "%{$term}%"));
        });
    }

    // ================= ACCESSORS =================
    public function getOutstandingAttribute(): float {
        return max(0, (float) $this->amount - (float) $this->amount_paid);
    }

    public function getIsOverdueAttribute(): bool {
        return $this->due_at && $this->due_at->isPast()
            && $this->outstanding > 0
            && !in_array($this->status, ['paid','cancelled','reversed','waived','expired'], true);
    }

    public function getIsSettledAttribute(): bool {
        return in_array($this->status, ['paid','waived','reversed','cancelled'], true);
    }

    public function getStatusColorAttribute(): string {
        return match ($this->status) {
            'pending'   => 'amber',
            'review'    => 'blue',
            'approved'  => 'indigo',
            'active'    => 'red',
            'disputed'  => 'fuchsia',
            'waived'    => 'cyan',
            'paid'      => 'emerald',
            'partial'   => 'lime',
            'overdue'   => 'rose',
            'cancelled' => 'slate',
            'reversed'  => 'zinc',
            'expired'   => 'stone',
            default     => 'slate',
        };
    }

    public function getSeverityColorAttribute(): string {
        return match ($this->severity) {
            'low'      => 'emerald',
            'medium'   => 'amber',
            'high'     => 'orange',
            'critical' => 'red',
            default    => 'slate',
        };
    }

    public function getDaysOverdueAttribute(): int {
        if (!$this->due_at || !$this->is_overdue) return 0;
        return (int) $this->due_at->diffInDays(now());
    }

    // ================= HELPERS =================
    public static function generateCaseNumber(): string
    {
        $year = now()->year;
        return DB::transaction(function () use ($year) {
            $count = static::withTrashed()->whereYear('created_at', $year)->lockForUpdate()->count();
            return sprintf('PEN-%d-%06d', $year, $count + 1);
        });
    }

    public function refreshOverdueStatus(): void
    {
        if ($this->is_overdue && $this->status === 'active') {
            $this->update(['status' => 'overdue']);
        }
    }

    public function addAudit(string $action, ?string $description = null, array $meta = []): AttendancePenaltyAuditLog
    {
        return AttendancePenaltyAuditLog::create([
            'penalty_id'  => $this->id,
            'actor_id'    => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'meta'        => $meta,
        ]);
    }
}