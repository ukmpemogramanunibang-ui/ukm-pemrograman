<?php

namespace App\Models\IdentityVault;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredentialAudit extends Model
{
    use HasFactory;

    protected $connection = 'identity_vault';
    protected $table = 'credential_audits';

    protected $fillable = [
        'credential_uuid', 'user_uuid', 'action',
        'ip_address', 'user_agent', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}