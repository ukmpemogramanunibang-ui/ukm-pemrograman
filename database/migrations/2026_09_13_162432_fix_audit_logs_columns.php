<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: kalau tabel belum ada, buat dulu dari nol
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $t) {
                $t->id();
                $t->uuid('event_uuid')->nullable()->unique();
                $t->string('actor_type', 32)->default('user');
                $t->unsignedBigInteger('actor_id')->nullable();
                $t->string('actor_name')->nullable();
                $t->string('actor_role')->nullable();
                $t->string('actor_position')->nullable();
                $t->unsignedBigInteger('target_user_id')->nullable();
                $t->string('action', 96)->nullable();
                $t->string('category', 48)->nullable();
                $t->string('severity', 24)->default('info');
                $t->string('result', 32)->default('success');
                $t->string('auditable_type')->nullable();
                $t->unsignedBigInteger('auditable_id')->nullable();
                $t->unsignedBigInteger('session_id')->nullable();
                $t->unsignedBigInteger('group_id')->nullable();
                $t->unsignedBigInteger('room_id')->nullable();
                $t->text('description')->nullable();
                $t->json('before_data')->nullable();
                $t->json('after_data')->nullable();
                $t->json('change_data')->nullable();
                $t->text('reason')->nullable();
                $t->string('permission', 128)->nullable();
                $t->string('authorization_result', 24)->nullable();
                $t->string('source', 32)->default('web');
                $t->string('ip_address', 45)->nullable();
                $t->text('user_agent')->nullable();
                $t->string('device_id', 128)->nullable();
                $t->string('request_id', 64)->nullable();
                $t->string('correlation_id', 64)->nullable();
                $t->unsignedInteger('affected_count')->default(0);
                $t->json('affected_ids')->nullable();
                $t->string('previous_hash', 64)->nullable();
                $t->string('integrity_hash', 64)->nullable();
                $t->boolean('is_sensitive')->default(false);
                $t->unsignedBigInteger('user_id')->nullable();
                $t->timestamps();

                // Indexes
                $t->index(['actor_id', 'created_at']);
                $t->index(['target_user_id', 'created_at']);
                $t->index(['action', 'created_at']);
                $t->index(['severity', 'created_at']);
                $t->index(['result', 'created_at']);
                $t->index(['source', 'created_at']);
                $t->index('session_id');
                $t->index('is_sensitive');
            });
            return;
        }

        // ─── Tabel sudah ada → tambah kolom yang HILANG saja ───
        Schema::table('audit_logs', function (Blueprint $t) {

            // UUID & actor context
            if (!Schema::hasColumn('audit_logs', 'event_uuid')) {
                $t->uuid('event_uuid')->nullable()->after('id');
            }
            if (!Schema::hasColumn('audit_logs', 'actor_type')) {
                $t->string('actor_type', 32)->default('user')->after('event_uuid');
            }
            if (!Schema::hasColumn('audit_logs', 'actor_id')) {
                $t->unsignedBigInteger('actor_id')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'actor_name')) {
                $t->string('actor_name')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'actor_role')) {
                $t->string('actor_role')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'actor_position')) {
                $t->string('actor_position')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'target_user_id')) {
                $t->unsignedBigInteger('target_user_id')->nullable();
            }

            // Action/severity/result
            if (!Schema::hasColumn('audit_logs', 'category')) {
                $t->string('category', 48)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'severity')) {
                $t->string('severity', 24)->default('info');
            }
            if (!Schema::hasColumn('audit_logs', 'result')) {
                $t->string('result', 32)->default('success');
            }

            // Entity universal
            if (!Schema::hasColumn('audit_logs', 'auditable_type')) {
                $t->string('auditable_type')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'auditable_id')) {
                $t->unsignedBigInteger('auditable_id')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'session_id')) {
                $t->unsignedBigInteger('session_id')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'group_id')) {
                $t->unsignedBigInteger('group_id')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'room_id')) {
                $t->unsignedBigInteger('room_id')->nullable();
            }

            // Snapshot / diff
            if (!Schema::hasColumn('audit_logs', 'before_data')) {
                $t->json('before_data')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'after_data')) {
                $t->json('after_data')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'change_data')) {
                $t->json('change_data')->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'reason')) {
                $t->text('reason')->nullable();
            }

            // Authorization
            if (!Schema::hasColumn('audit_logs', 'permission')) {
                $t->string('permission', 128)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'authorization_result')) {
                $t->string('authorization_result', 24)->nullable();
            }

            // Device/tracing
            if (!Schema::hasColumn('audit_logs', 'source')) {
                $t->string('source', 32)->default('web');
            }
            if (!Schema::hasColumn('audit_logs', 'device_id')) {
                $t->string('device_id', 128)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'request_id')) {
                $t->string('request_id', 64)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'correlation_id')) {
                $t->string('correlation_id', 64)->nullable();
            }

            // Impact
            if (!Schema::hasColumn('audit_logs', 'affected_count')) {
                $t->unsignedInteger('affected_count')->default(0);
            }
            if (!Schema::hasColumn('audit_logs', 'affected_ids')) {
                $t->json('affected_ids')->nullable();
            }

            // Integrity
            if (!Schema::hasColumn('audit_logs', 'previous_hash')) {
                $t->string('previous_hash', 64)->nullable();
            }
            if (!Schema::hasColumn('audit_logs', 'integrity_hash')) {
                $t->string('integrity_hash', 64)->nullable();
            }

            // Flags
            if (!Schema::hasColumn('audit_logs', 'is_sensitive')) {
                $t->boolean('is_sensitive')->default(false);
            }
        });

        // Tambahkan indexes secara terpisah (aman kalau duplikat)
        try {
            Schema::table('audit_logs', function (Blueprint $t) {
                $t->index(['actor_id', 'created_at'], 'audit_logs_actor_created_idx');
            });
        } catch (\Throwable $e) { /* index sudah ada */ }

        try {
            Schema::table('audit_logs', function (Blueprint $t) {
                $t->index(['severity', 'created_at'], 'audit_logs_sev_created_idx');
            });
        } catch (\Throwable $e) { /* index sudah ada */ }

        try {
            Schema::table('audit_logs', function (Blueprint $t) {
                $t->index(['result', 'created_at'], 'audit_logs_res_created_idx');
            });
        } catch (\Throwable $e) { /* index sudah ada */ }

        try {
            Schema::table('audit_logs', function (Blueprint $t) {
                $t->index(['session_id'], 'audit_logs_session_idx');
            });
        } catch (\Throwable $e) { /* index sudah ada */ }
    }

    public function down(): void
    {
        // Tidak ada down — kita tidak mau drop kolom audit historis.
        // Kalau perlu rollback, drop tabel manual.
    }
};