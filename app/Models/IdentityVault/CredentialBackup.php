<?php

namespace App\Models\IdentityVault;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CredentialBackup extends Model
{
    use HasFactory;

    protected $connection = 'identity_vault';
    protected $table = 'credential_backups';

    protected $fillable = [
        'credential_uuid', 'snapshot', 'checksum', 'backup_at'
    ];

    protected $casts = [
        'snapshot' => 'array',
        'backup_at' => 'datetime',
    ];

    public function identity()
    {
        return $this->belongsTo(IdentityRegistry::class, 'credential_uuid', 'credential_uuid');
    }
}