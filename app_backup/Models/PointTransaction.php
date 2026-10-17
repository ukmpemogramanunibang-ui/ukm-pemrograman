<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointTransaction extends Model
{
    protected $table = 'point_transactions';

    protected $fillable = [
        // Skema asli
        'point_account_id',
        'type',
        'amount',
        'reason',
        'source_type',
        'source_id',
        'created_by',

        // Extended
        'user_id',
        'points',
        'entry_type',
        'attendance_session_id',
        'attendance_record_id',
        'attendance_participant_id',
        'point_rule_id',
        'status',
        'reverses_transaction_id',
        'corrected_by',
        'finalized_at',
        'reversed_at',
        'reversal_reason',
        'period_key',
        'idempotency_key',
        'meta',
    ];

    protected $casts = [
        'amount'     => 'integer',
        'points'     => 'integer',
        'meta'       => 'array',
        'finalized_at' => 'datetime',
        'reversed_at'  => 'datetime',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================
    public function account(): BelongsTo
    {
        return $this->belongsTo(PointAccount::class, 'point_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function correctedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AttendancePointRule::class, 'point_rule_id');
    }

    public function reverses(): BelongsTo
    {
        return $this->belongsTo(PointTransaction::class, 'reverses_transaction_id');
    }

    public function reversals(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'reverses_transaction_id');
    }

    // ============================================================
    // SCOPES
    // ============================================================
    public function scopeFinal(Builder $q): Builder
    {
        return $q->where(function ($w) {
            $w->whereNull('status')->orWhere('status', 'final');
        });
    }

    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    public function scopeReversed(Builder $q): Builder
    {
        return $q->where('status', 'reversed');
    }

    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    public function scopeBetweenDates(Builder $q, $from, $to): Builder
    {
        return $q->whereBetween('created_at', [$from, $to]);
    }

    public function scopeCredits(Builder $q): Builder
    {
        return $q->where('type', 'credit');
    }

    public function scopeDebits(Builder $q): Builder
    {
        return $q->where('type', 'debit');
    }

    public function scopeEntryType(Builder $q, string|array $type): Builder
    {
        return is_array($type) ? $q->whereIn('entry_type', $type) : $q->where('entry_type', $type);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================
    public function getSignedPointsAttribute(): int
    {
        if ($this->points !== null) return (int) $this->points;
        return $this->type === 'debit' ? -1 * (int) $this->amount : (int) $this->amount;
    }

    public function getIsCreditAttribute(): bool
    {
        return $this->signed_points >= 0;
    }

    public function getIsReversalAttribute(): bool
    {
        return $this->entry_type === 'REVERSAL' || $this->reverses_transaction_id !== null;
    }

    // ============================================================
    // AUTO-SYNC: type/amount ⇄ points, user_id ⇄ point_account_id
    // ============================================================
    protected static function booted(): void
    {
        static::saving(function (self $tx) {

            // ---- 1. Sync type/amount ⇄ points ----
            $pointsDirty = $tx->isDirty('points');
            $taDirty     = $tx->isDirty('type') || $tx->isDirty('amount');

            if ($pointsDirty && !$taDirty && $tx->points !== null) {
                // Sumber: points → turunkan type+amount
                $p = (int) $tx->points;
                $tx->type   = $p >= 0 ? 'credit' : 'debit';
                $tx->amount = abs($p);
            } elseif ($taDirty && !$pointsDirty) {
                // Sumber: type+amount → turunkan points
                $tx->points = $tx->type === 'debit'
                    ? -1 * abs((int) $tx->amount)
                    :      abs((int) $tx->amount);
            } elseif ($pointsDirty && $taDirty) {
                // Keduanya di-set — pastikan konsisten: points menang
                $p = (int) $tx->points;
                $tx->type   = $p >= 0 ? 'credit' : 'debit';
                $tx->amount = abs($p);
            }

            // ---- 2. Auto-resolve point_account_id ⇄ user_id ----
            if (!$tx->point_account_id && $tx->user_id) {
                $tx->point_account_id = PointAccount::forUser((int) $tx->user_id)->id;
            }
            if (!$tx->user_id && $tx->point_account_id) {
                $tx->user_id = PointAccount::whereKey($tx->point_account_id)->value('user_id');
            }

            // ---- 3. Default entry_type ----
            if (!$tx->entry_type) {
                $tx->entry_type = $tx->source_type ?: 'MANUAL_ADJUSTMENT';
            }

            // ---- 4. Default status ----
            if (!$tx->status) {
                $tx->status = 'final';
            }

            // ---- 5. period_key otomatis ----
            if (!$tx->period_key) {
                $when = $tx->created_at ?? now();
                $tx->period_key = $when->format('Y-m');
            }

            // ---- 6. created_by default ke auth user ----
            if (!$tx->created_by && auth()->check()) {
                $tx->created_by = auth()->id();
            }
        });

        // ---- Auto-recalc saldo akun setelah transaksi final dibuat ----
        static::created(function (self $tx) {
            if ($tx->point_account_id && in_array($tx->status, ['final', null], true)) {
                optional($tx->account)->recalculateBalance();
            }
        });

        static::updated(function (self $tx) {
            if ($tx->isDirty('status') && $tx->point_account_id) {
                optional($tx->account)->recalculateBalance();
            }
        });
    }

    // ============================================================
    // FACTORY HELPERS — panggil ini dari service lain
    // ============================================================

    /**
     * Catat poin dengan idempotency key (mencegah duplikasi NFC/barcode).
     */
    public static function record(array $attributes): self
    {
        // Idempotency: kalau key sudah ada, kembalikan yang lama
        if (!empty($attributes['idempotency_key'])) {
            $existing = static::where('idempotency_key', $attributes['idempotency_key'])->first();
            if ($existing) return $existing;
        }

        return static::create($attributes);
    }

    /**
     * Buat reversal untuk transaksi ini.
     */
    public function createReversal(string $reason, ?int $byUserId = null): self
    {
        if ($this->status === 'reversed') {
            throw new \RuntimeException('Transaksi sudah pernah di-reversal.');
        }

        $reversal = static::create([
            'point_account_id'        => $this->point_account_id,
            'user_id'                 => $this->user_id,
            'entry_type'              => 'REVERSAL',
            'points'                  => -1 * (int) $this->signed_points,
            'reason'                  => $reason,
            'reversal_reason'         => $reason,
            'reverses_transaction_id' => $this->id,
            'corrected_by'            => $byUserId ?? auth()->id(),
            'reversed_at'             => now(),
            'status'                  => 'final',
            'attendance_session_id'   => $this->attendance_session_id,
            'attendance_record_id'    => $this->attendance_record_id,
        ]);

        $this->update([
            'status'          => 'reversed',
            'reversed_at'     => now(),
            'reversal_reason' => $reason,
        ]);

        return $reversal;
    }
}