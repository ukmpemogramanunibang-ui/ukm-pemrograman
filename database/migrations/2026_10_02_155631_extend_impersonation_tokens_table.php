<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ─────────────────────────────────────────────────
         | Kalau tabel belum ada → create dari nol
         * ───────────────────────────────────────────────── */
        if (! Schema::hasTable('impersonation_tokens')) {
            Schema::create('impersonation_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('token', 64)->unique();
                $table->unsignedBigInteger('owner_id')->index();
                $table->unsignedBigInteger('target_user_id')->index();
                $table->string('role_slug', 32)->index();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamp('revoked_at')->nullable()->index();
                $table->timestamps();
                $table->index(['owner_id', 'expires_at'], 'imp_tokens_owner_exp_idx');
            });
            return;
        }

        /* ─────────────────────────────────────────────────
         | Tabel sudah ada → tambah kolom yang belum ada
         | (SAFE: cek satu-satu, TANPA ->after())
         * ───────────────────────────────────────────────── */
        $existing = Schema::getColumnListing('impersonation_tokens');

        Schema::table('impersonation_tokens', function (Blueprint $table) use ($existing) {

            // Token string
            if (! in_array('token', $existing, true)) {
                $table->string('token', 64)->nullable();
            }
            // Owner
            if (! in_array('owner_id', $existing, true)) {
                $table->unsignedBigInteger('owner_id')->nullable();
            }
            // Target user (bisa jadi user_id / target_id / target_user_id)
            if (! in_array('target_user_id', $existing, true)
                && ! in_array('target_id', $existing, true)
                && ! in_array('user_id', $existing, true)) {
                $table->unsignedBigInteger('target_user_id')->nullable();
            }
            // Role slug
            if (! in_array('role_slug', $existing, true)) {
                $table->string('role_slug', 32)->nullable();
            }
            // IP
            if (! in_array('ip_address', $existing, true)) {
                $table->string('ip_address', 45)->nullable();
            }
            // User agent
            if (! in_array('user_agent', $existing, true)) {
                $table->string('user_agent', 500)->nullable();
            }
            // Timestamps
            if (! in_array('used_at', $existing, true)) {
                $table->timestamp('used_at')->nullable();
            }
            if (! in_array('expires_at', $existing, true)) {
                $table->timestamp('expires_at')->nullable();
            }
            if (! in_array('revoked_at', $existing, true)) {
                $table->timestamp('revoked_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('impersonation_tokens')) return;

        Schema::table('impersonation_tokens', function (Blueprint $table) {
            $cols = ['used_at', 'ip_address', 'user_agent', 'expires_at', 'revoked_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('impersonation_tokens', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};