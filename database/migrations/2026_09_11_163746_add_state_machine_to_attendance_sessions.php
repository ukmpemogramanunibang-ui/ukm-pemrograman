<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════════════
        // 1. attendance_sessions
        // ═══════════════════════════════════════════════════════════════
        if (Schema::hasTable('attendance_sessions')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $has = fn ($col) => Schema::hasColumn('attendance_sessions', $col);

                if (! $has('state_version'))    $table->unsignedInteger('state_version')->default(1);
                if (! $has('previous_status'))  $table->string('previous_status')->nullable();
                if (! $has('paused_at'))        $table->timestamp('paused_at')->nullable();
                if (! $has('resumed_at'))       $table->timestamp('resumed_at')->nullable();
                if (! $has('reconciled_at'))    $table->timestamp('reconciled_at')->nullable();
                if (! $has('security_context')) $table->json('security_context')->nullable();
                if (! $has('idempotency_key'))  $table->string('idempotency_key', 64)->nullable();
            });

            // Index — guard terpisah karena bisa sudah ada
            try {
                Schema::table('attendance_sessions', function (Blueprint $table) {
                    $table->index(['room_id', 'scheduled_start_at', 'scheduled_end_at'], 'idx_room_time_overlap');
                });
            } catch (\Throwable $e) {}

            try {
                Schema::table('attendance_sessions', function (Blueprint $table) {
                    $table->index(['session_status', 'attendance_open_at', 'attendance_close_at'], 'idx_status_window');
                });
            } catch (\Throwable $e) {}
        }

        // ═══════════════════════════════════════════════════════════════
        // 2. attendance_records
        // ═══════════════════════════════════════════════════════════════
        if (Schema::hasTable('attendance_records')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $has = fn ($col) => Schema::hasColumn('attendance_records', $col);

                if (! $has('confidence_score'))    $table->unsignedTinyInteger('confidence_score')->default(100);
                if (! $has('risk_score'))          $table->unsignedTinyInteger('risk_score')->default(0);
                if (! $has('verification_meta'))   $table->json('verification_meta')->nullable();
                if (! $has('idempotency_key'))     $table->string('idempotency_key', 64)->nullable();

                if (! $has('corrected_from_id'))   $table->foreignId('corrected_from_id')->nullable()->constrained('attendance_records')->nullOnDelete();
                if (! $has('corrected_by_id'))     $table->foreignId('corrected_by_id')->nullable()->constrained('users')->nullOnDelete();
                if (! $has('corrected_at'))        $table->timestamp('corrected_at')->nullable();
                if (! $has('correction_reason'))   $table->text('correction_reason')->nullable();
            });

            // Drop index lama — guard try/catch karena index mungkin sudah tidak ada
            try {
                Schema::table('attendance_records', function (Blueprint $table) {
                    $table->dropUnique('unique_session_user_record');
                });
            } catch (\Throwable $e) {
                // Index tidak ada — aman
            }

            // Buat index baru
            try {
                Schema::table('attendance_records', function (Blueprint $table) {
                    $table->unique(['session_id', 'user_id'], 'unique_active_session_user');
                });
            } catch (\Throwable $e) {
                // Index sudah ada — aman
            }
        }

        // ═══════════════════════════════════════════════════════════════
        // 3. attendance_audit_logs
        // ═══════════════════════════════════════════════════════════════
        if (Schema::hasTable('attendance_audit_logs')) {
            Schema::table('attendance_audit_logs', function (Blueprint $table) {
                $has = fn ($col) => Schema::hasColumn('attendance_audit_logs', $col);

                if (! $has('event_id'))     $table->string('event_id', 64)->nullable();
                if (! $has('request_id'))   $table->string('request_id', 64)->nullable();
                if (! $has('actor_role'))   $table->string('actor_role', 32)->nullable();
                if (! $has('entity_type'))  $table->string('entity_type', 64)->nullable();
                if (! $has('entity_id'))    $table->string('entity_id', 64)->nullable();
                if (! $has('before_hash'))  $table->string('before_hash', 64)->nullable();
                if (! $has('after_hash'))   $table->string('after_hash', 64)->nullable();
                if (! $has('prev_log_hash')) $table->string('prev_log_hash', 64)->nullable();
                if (! $has('chain_hash'))   $table->string('chain_hash', 64)->nullable();
            });
        }
    }

    public function down(): void
    {
        // No-op — kolom audit tidak boleh di-drop sembarangan
    }
};