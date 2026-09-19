<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class QrAuditLog
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $qr_code_id
 * @property string $action
 * @property string $status
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property array|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 *
 * @property-read User|null $user
 * @property-read MemberQrCode|null $qrCode
 */
class QrAuditLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'qr_audit_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'qr_code_id',
        'action',
        'status',
        'ip_address',
        'user_agent',
        'details',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The name of the "created at" column.
     */
    const CREATED_AT = 'created_at';

    // ================================================================
    // RELASI
    // ================================================================

    /**
     * User yang melakukan aksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * QR Code yang terkena aksi.
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(MemberQrCode::class, 'qr_code_id');
    }

    // ================================================================
    // SCOPES
    // ================================================================

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    // ================================================================
    // ACCESSORS
    // ================================================================

    /**
     * Dapatkan label action yang lebih mudah dibaca.
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'generate'     => 'Generate QR',
            'regenerate'   => 'Regenerate QR',
            'revoke'       => 'Revoke QR',
            'block'        => 'Blokir QR',
            'activate'     => 'Aktifkan QR',
            'extend'       => 'Perpanjang QR',
            'print'        => 'Cetak QR',
            'download'     => 'Download QR',
            'scan'         => 'Scan QR',
            'login'        => 'Login via QR',
            'failed_scan'  => 'Scan Gagal',
            'save'         => 'Simpan QR',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Dapatkan badge status (HTML).
     */
    public function getStatusBadgeAttribute(): string
    {
        $class = match ($this->status) {
            'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
            'failed'  => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            default   => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
        $label = $this->status === 'success' ? 'Berhasil' : ($this->status === 'failed' ? 'Gagal' : ucfirst($this->status));
        return sprintf('<span class="px-2 py-0.5 rounded-full text-xs font-medium %s">%s</span>', $class, $label);
    }
}