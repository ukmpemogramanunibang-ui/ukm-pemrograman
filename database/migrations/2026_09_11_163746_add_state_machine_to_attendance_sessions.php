<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            // State machine
            $table->unsignedInteger('state_version')->default(1)->after('session_status');
            $table->string('previous_status')->nullable()->after('state_version');
            $table->timestamp('paused_at')->nullable()->after('cancelled_at');
            $table->timestamp('resumed_at')->nullable()->after('paused_at');
            $table->timestamp('reconciled_at')->nullable()->after('resumed_at');

            // Conflict detection
            $table->index(['room_id', 'scheduled_start_at', 'scheduled_end_at'], 'idx_room_time_overlap');
            $table->index(['session_status', 'attendance_open_at', 'attendance_close_at'], 'idx_status_window');

            // Security
            $table->json('security_context')->nullable()->after('notes');
            $table->string('idempotency_key', 64)->nullable()->unique()->after('uuid');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            // Fraud & confidence
            $table->unsignedTinyInteger('confidence_score')->default(100)->after('points');
            $table->unsignedTinyInteger('risk_score')->default(0)->after('confidence_score');
            $table->json('verification_meta')->nullable()->after('user_agent');

            // Idempotency
            $table->string('idempotency_key', 64)->nullable()->unique()->after('uuid');

            // Correction link
            $table->foreignId('corrected_from_id')->nullable()->after('verified_by')
                  ->constrained('attendance_records')->nullOnDelete();
            $table->foreignId('corrected_by_id')->nullable()->after('corrected_from_id')
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('corrected_at')->nullable()->after('corrected_by_id');
            $table->text('correction_reason')->nullable()->after('corrected_at');

            // Remove old unique, replace with partial unique (MySQL 8)
            $table->dropUnique('unique_session_user_record');
            $table->unique(['session_id', 'user_id'], 'unique_active_session_user');
        });

        Schema::table('attendance_audit_logs', function (Blueprint $table) {
            $table->string('event_id', 64)->nullable()->index()->after('id');
            $table->string('request_id', 64)->nullable()->index()->after('event_id');
            $table->string('actor_role', 32)->nullable()->after('actor_id');
            $table->string('entity_type', 64)->nullable()->after('action');
            $table->string('entity_id', 64)->nullable()->after('entity_type');
            $table->string('before_hash', 64)->nullable()->after('before_data');
            $table->string('after_hash', 64)->nullable()->after('after_data');
            $table->string('prev_log_hash', 64)->nullable()->after('after_hash');
            $table->string('chain_hash', 64)->nullable()->index()->after('prev_log_hash');
        });
    }

    public function down(): void
    {
        // reverse...
    }
};