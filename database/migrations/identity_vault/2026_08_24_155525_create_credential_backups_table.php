<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('identity_vault')->create('credential_backups', function (Blueprint $table) {
            $table->id();
            $table->uuid('credential_uuid');
            $table->json('snapshot');
            $table->string('checksum', 64);
            $table->timestamp('backup_at')->nullable();
            $table->timestamps();

            $table->foreign('credential_uuid')
                  ->references('credential_uuid')->on('identity_registry')
                  ->onDelete('cascade');
            $table->index(['credential_uuid']);
        });
    }

    public function down(): void
    {
        Schema::connection('identity_vault')->dropIfExists('credential_backups');
    }
};