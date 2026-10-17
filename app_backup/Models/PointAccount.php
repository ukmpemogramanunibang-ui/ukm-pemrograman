<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointAccount extends Model
{
    protected $table = 'point_accounts';

    protected $fillable = [
        'user_id',
        'balance',
        'total_credit',
        'total_debit',
        'status',
        'meta',
    ];

    protected $casts = [
        'balance'      => 'integer',
        'total_credit' => 'integer',
        'total_debit'  => 'integer',
        'meta'         => 'array',
    ];

    // ============================================================
    // RELATIONS
    // ============================================================
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'point_account_id');
    }

    // ============================================================
    // SCOPES
    // ============================================================
    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeWithActiveTransaction($q)
    {
        return $q->whereHas('transactions', fn ($t) => $t->whereIn('status', ['final', null]));
    }

    // ============================================================
    // HELPERS — sumber kebenaran tunggal
    // ============================================================
    /**
     * Hitung ulang saldo dari ledger (hanya transaksi final, reversed tidak dihitung dua kali).
     */
    public function recalculateBalance(bool $persist = true): int
    {
        $q = $this->transactions()
            ->where(function ($w) {
                $w->whereNull('status')->orWhereIn('status', ['final']);
            });

        // Debit dihitung negatif, credit positif (via kolom `points` yang sudah signed)
        $credits = (clone $q)->where('type', 'credit')->sum('amount');
        $debits  = (clone $q)->where('type', 'debit')->sum('amount');

        $balance     = (int) $credits - (int) $debits;
        $totalCredit = (int) $credits;
        $totalDebit  = (int) $debits;

        if ($persist) {
            $this->forceFill([
                'balance'      => $balance,
                'total_credit' => $totalCredit,
                'total_debit'  => $totalDebit,
            ])->save();
        }

        return $balance;
    }

    /**
     * Ambil akun untuk user, buat kalau belum ada.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'total_credit' => 0, 'total_debit' => 0]
        );
    }

    // ============================================================
    // BOOT — auto recalc saat transaksi baru dibuat
    // ============================================================
    protected static function booted(): void
    {
        // Kalau kolom balance tidak ada, skip semua persistence
        static::saving(function (self $account) {
            if (!\Schema::hasColumn('point_accounts', 'balance')) {
                $account->offsetUnset('balance');
            }
        });
    }
}