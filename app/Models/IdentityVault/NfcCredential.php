<?php

namespace App\Models\IdentityVault;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NfcCredential extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'identity_vault';
    protected $table = 'nfc_credentials';

    protected $fillable = [
        'credential_uuid', 'nfc_identifier_hash', 'nfc_public_id',
        'version', 'signature', 'is_active', 'last_seen_at',
        'registered_at', 'revoked_at'
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'registered_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function identity()
    {
        return $this->belongsTo(IdentityRegistry::class, 'credential_uuid', 'credential_uuid');
    }
}