<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_otp_challenges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('otp_hash');

            $table->timestamp('expires_at');

            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(5);

            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('invalidated_at')->nullable();

            $table->string('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // ✅ Nama index dipendekkan supaya tidak melebihi 64 char MySQL
            $table->index(['user_id', 'expires_at'], 'protc_user_expires_idx');
            $table->index(['user_id', 'verified_at', 'invalidated_at'], 'protc_user_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otp_challenges');
    }
};