<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NfcCredential extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_REVOKED  = 'revoked';
    public const STATUS_PENDING  = 'pending';

    protected $fillable = [
        'user_id',
        'nfc_uuid',
        'nfc_identifier',
        'token',
        'status',
        'is_active',
        'is_revoked',
        'generated_at',
        'registered_at',
        'revoked_at',
        'expired_at',
        'last_used_at',
        'scan_count',
        'nfc_image',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'generated_at'  => 'datetime',
            'registered_at' => 'datetime',
            'revoked_at'    => 'datetime',
            'expired_at'    => 'datetime',
            'last_used_at'  => 'datetime',
            'is_active'     => 'boolean',
            'is_revoked'    => 'boolean',
            'scan_count'    => 'integer',
        ];
    }

    /* -------------------------------------------------------------------- */
    /* RELATIONS                                                             */
    /* -------------------------------------------------------------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* -------------------------------------------------------------------- */
    /* SCOPES                                                                */
    /* -------------------------------------------------------------------- */

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('is_active', true)
            ->where('is_revoked', false)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            });
    }

    public function scopeRevoked($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_REVOKED)
              ->orWhere('is_revoked', true);
        });
    }

     public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
 public function isActive(): bool
    {
        return (bool) (
            $this->is_active
            && !$this->is_revoked
            && $this->status === self::STATUS_ACTIVE
            && (!$this->expired_at || $this->expired_at->isFuture())
        );
    }
    /* -------------------------------------------------------------------- */
    /* ACCESSORS                                                             */
    /* -------------------------------------------------------------------- */

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_revoked || $this->status === self::STATUS_REVOKED) {
            return 'Dicabut';
        }
        if (!$this->is_active || $this->status === self::STATUS_INACTIVE) {
            return 'Nonaktif';
        }
        if ($this->expired_at && $this->expired_at->isPast()) {
            return 'Kedaluwarsa';
        }
        return match ($this->status) {
            self::STATUS_ACTIVE  => 'Aktif',
            self::STATUS_PENDING => 'Menunggu',
            default              => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match (true) {
            $this->is_revoked || $this->status === self::STATUS_REVOKED  => 'red',
            !$this->is_active || $this->status === self::STATUS_INACTIVE => 'yellow',
            $this->expired_at && $this->expired_at->isPast()             => 'gray',
            $this->status === self::STATUS_ACTIVE                        => 'green',
            default                                                      => 'gray',
        };
    }

    /* -------------------------------------------------------------------- */
    /* HELPERS                                                               */
    /* -------------------------------------------------------------------- */

    public static function userHasActiveNfc(int $userId): ?self
    {
        return static::forUser($userId)->active()->first();
    }

    public static function cardOwnedByOther(string $nfcUuid, ?int $exceptUserId = null): ?self
    {
        $query = static::where('nfc_uuid', $nfcUuid)->active();
        if ($exceptUserId !== null) {
            $query->where('user_id', '!=', $exceptUserId);
        }
        return $query->with('user')->first();
    }

    public static function identifierOwnedByOther(string $identifier, ?int $exceptUserId = null): ?self
    {
        $query = static::where('nfc_identifier', $identifier)->active();
        if ($exceptUserId !== null) {
            $query->where('user_id', '!=', $exceptUserId);
        }
        return $query->with('user')->first();
    }

    /* -------------------------------------------------------------------- */
    /* ACTIONS                                                               */
    /* -------------------------------------------------------------------- */

    public function revoke(?string $reason = null): void
    {
        $this->update([
            'status'     => self::STATUS_REVOKED,
            'is_active'  => false,
            'is_revoked' => true,
            'revoked_at' => now(),
            'notes'      => $reason,
        ]);
    }

    public function moveTo(int $newUserId): void
    {
        $this->update([
            'user_id'       => $newUserId,
            'status'        => self::STATUS_ACTIVE,
            'is_active'     => true,
            'is_revoked'    => false,
            'registered_at' => now(),
        ]);
    }

    /* -------------------------------------------------------------------- */
    /* ROUTE KEY                                                             */
    /* -------------------------------------------------------------------- */

    public function getRouteKeyName(): string
    {
        return 'nfc_uuid';
    }
}