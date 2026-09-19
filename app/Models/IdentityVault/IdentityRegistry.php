<?php

namespace App\Models\IdentityVault;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdentityRegistry extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'identity_vault';
    protected $table = 'identity_registry';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'credential_uuid', 'credential_code', 'user_uuid', 'member_number',
        'account_type', 'status', 'qr_enabled', 'nfc_enabled',
        'account_snapshot', 'revoked_at'
    ];

    protected $casts = [
        'account_snapshot' => 'array',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function nfcCredentials()
    {
        return $this->hasMany(NfcCredential::class, 'credential_uuid', 'credential_uuid');
    }

    public function audits()
    {
        return $this->hasMany(CredentialAudit::class, 'credential_uuid', 'credential_uuid');
    }

    public function backups()
    {
        return $this->hasMany(CredentialBackup::class, 'credential_uuid', 'credential_uuid');
    }

    // Helper: generate credential_code
    public static function generateCredentialCode($id)
    {
        return 'UNICODE-CRED-' . str_pad($id, 8, '0', STR_PAD_LEFT);
    }
}