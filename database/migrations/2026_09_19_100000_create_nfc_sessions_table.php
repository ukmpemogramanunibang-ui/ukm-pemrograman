<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfc_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Token dari website (dibuat sebelum app dibuka)
            $table->string('session_token', 64)->unique();

            // Token one-time untuk handoff ke browser
            $table->string('handoff_token', 128)->nullable()->unique();

            // Konteks / intent: login, attendance, cash, event, profile
            $table->string('intent', 32)->default('login');

            // Relasi user (diisi setelah NFC di-tap)
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')->nullOnDelete();

            // Relasi event opsional
            $table->unsignedBigInteger('event_id')->nullable();

            // URL kembali setelah handoff
            $table->string('return_url', 512);

            // Info device
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 512)->nullable();

            // Lifecycle
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index(['session_token', 'expires_at'], 'idx_nfc_session_lookup');
            $table->index('handoff_token', 'idx_nfc_handoff_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfc_sessions');
    }
};
