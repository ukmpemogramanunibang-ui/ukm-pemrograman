<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('identity_vault')->create('credential_audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('credential_uuid')->nullable();
            $table->uuid('user_uuid')->nullable();
            $table->string('action', 50); // login, invalid_password, nfc_scan, qr_scan, etc.
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['credential_uuid']);
            $table->index(['action']);
        });
    }

    public function down(): void
    {
        Schema::connection('identity_vault')->dropIfExists('credential_audits');
    }
};