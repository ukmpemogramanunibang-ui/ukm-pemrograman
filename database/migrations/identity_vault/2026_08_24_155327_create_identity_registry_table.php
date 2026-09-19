<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('identity_vault')->create('identity_registry', function (Blueprint $table) {
            $table->id();
            $table->uuid('credential_uuid')->unique();
            $table->string('credential_code', 50)->unique(); // UNICODE-CRED-00000036
            $table->uuid('user_uuid')->nullable(); // referensi ke users.user_uuid
            $table->string('member_number', 50)->nullable();
            $table->string('account_type', 20)->default('official');
            $table->enum('status', ['active', 'blocked', 'lost', 'stolen', 'revoked', 'expired', 'recovery'])->default('active');
            $table->boolean('qr_enabled')->default(true);
            $table->boolean('nfc_enabled')->default(false);
            $table->json('account_snapshot')->nullable(); // snapshot data akun
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_uuid']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::connection('identity_vault')->dropIfExists('identity_registry');
    }
};