<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('identity_vault')->create('nfc_credentials', function (Blueprint $table) {
            $table->id();
            $table->uuid('credential_uuid');
            $table->string('nfc_identifier_hash', 64)->unique(); // hash UID NFC
            $table->string('nfc_public_id', 50)->nullable();
            $table->integer('version')->default(1);
            $table->string('signature', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('credential_uuid')
                  ->references('credential_uuid')->on('identity_registry')
                  ->onDelete('cascade');
            $table->index(['credential_uuid']);
            $table->index(['nfc_identifier_hash']);
        });
    }

    public function down(): void
    {
        Schema::connection('identity_vault')->dropIfExists('nfc_credentials');
    }
};