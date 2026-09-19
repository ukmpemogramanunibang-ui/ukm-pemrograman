<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // 1. ATTENDANCE SESSIONS
        // ============================================================
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('schedule_id')->nullable()->constrained('room_schedules')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();

            // Type & Mode
            $table->enum('attendance_type', [
                'regular', 'event', 'competition', 'meeting',
                'training', 'workshop', 'tournament', 'external', 'custom'
            ])->default('regular');
            $table->enum('attendance_mode', ['offline', 'online', 'hybrid'])->default('offline');

            // Time Points
            $table->timestamp('scheduled_start_at')->nullable();
            $table->timestamp('scheduled_end_at')->nullable();
            $table->timestamp('attendance_open_at')->nullable();
            $table->timestamp('attendance_close_at')->nullable();
            $table->timestamp('session_end_at')->nullable();
            $table->integer('grace_period_minutes')->default(0);
            $table->integer('early_open_minutes')->default(15);
            $table->integer('extension_minutes')->default(0);

            // Status
            $table->enum('session_status', [
                'draft', 'scheduled', 'open', 'paused',
                'extended', 'closed', 'expired', 'cancelled', 'archived'
            ])->default('draft');

            // Methods allowed (JSON: ["manual","nfc","qr"])
            $table->json('allowed_methods')->nullable();

            // Target
            $table->enum('target_type', ['group', 'role', 'individual', 'mixed', 'all'])->default('group');
            $table->json('target_data')->nullable();
            $table->integer('target_count')->default(0);

            // Shift
            $table->string('shift_name')->nullable();
            $table->integer('shift_order')->default(0);

            // Meta
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('extended_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['session_status', 'attendance_open_at']);
            $table->index(['scheduled_start_at', 'scheduled_end_at']);
        });

        // ============================================================
        // 2. ATTENDANCE TARGETS (Siapa yang harus hadir)
        // ============================================================
        Schema::create('attendance_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();

            $table->enum('target_mode', ['offline', 'online', 'undetermined'])->default('undetermined');
            $table->boolean('is_excluded')->default(false);
            $table->text('exclude_reason')->nullable();

            // Snapshot (historical accuracy)
            $table->string('snapshot_name')->nullable();
            $table->string('snapshot_nim')->nullable();
            $table->string('snapshot_role')->nullable();
            $table->string('snapshot_group_name')->nullable();
            $table->string('snapshot_division_name')->nullable();

            $table->timestamps();
            $table->unique(['session_id', 'user_id'], 'unique_session_user_target');
            $table->index(['session_id', 'is_excluded']);
        });
 // ============================================================
        // 4. ATTENDANCE PERMISSIONS (Izin)
        // ============================================================
        Schema::create('attendance_permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('permission_type', [
                'excused_absent', 'late_excused', 'late_request', 'correction'
            ]);
            $table->text('reason');
            $table->string('evidence_path')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->boolean('is_late_request')->default(false);

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['session_id', 'status']);
        });
        // ============================================================
        // 3. ATTENDANCE RECORDS (Kehadiran sebenarnya)
        // ============================================================
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_id')->nullable()->constrained('attendance_targets')->nullOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('room_schedules')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();

            // Snapshot
            $table->string('snapshot_name');
            $table->string('snapshot_nim')->nullable();
            $table->string('snapshot_role')->nullable();
            $table->string('snapshot_group_name')->nullable();
            $table->string('snapshot_division_name')->nullable();
            $table->string('snapshot_room_name')->nullable();

            // Mode & Method
            $table->enum('attendance_mode', ['offline', 'online', 'undetermined'])->default('undetermined');
            $table->enum('method', ['manual', 'nfc', 'qr', 'manual_by_officer', 'historical', 'auto_system'])->default('manual');

            // Status
            $table->enum('status', [
                'pending', 'present', 'late', 'late_excused',
                'excused_absent', 'absent', 'absent_excused',
                'manual', 'historical', 'rejected', 'corrected'
            ])->default('pending');
            $table->string('sub_status')->nullable();

            $table->timestamp('recorded_at')->nullable();
            $table->integer('points')->default(0);
            $table->foreignId('permission_id')->nullable()->constrained('attendance_permissions')->nullOnDelete();

            // Manual by officer
            $table->boolean('is_manual_by_officer')->default(false);
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('manual_reason')->nullable();
            $table->string('failed_method')->nullable();

            // Device
            $table->string('ip_address', 45)->nullable();
            $table->string('device_identifier')->nullable();
            $table->string('user_agent')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // ONE USER = ONE VALID RECORD PER SESSION
            $table->unique(['session_id', 'user_id', 'deleted_at'], 'unique_session_user_record');
            $table->index(['session_id', 'status']);
            $table->index(['user_id', 'recorded_at']);
        });

       

        // ============================================================
        // 5. ATTENDANCE POINT LEDGER (Immutable)
        // ============================================================
        Schema::create('attendance_point_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $table->foreignId('record_id')->nullable()->constrained('attendance_records')->nullOnDelete();

            $table->integer('points');
            $table->enum('entry_type', ['earned', 'correction', 'override', 'penalty', 'bonus', 'adjustment']);
            $table->text('reason')->nullable();
            $table->integer('balance_after')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // ============================================================
        // 6. ATTENDANCE AUDIT LOGS
        // ============================================================
        Schema::create('attendance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
            $table->index(['actor_id', 'created_at']);
        });

        // ============================================================
        // 7. ATTENDANCE SECURITY EVENTS
        // ============================================================
        Schema::create('attendance_security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $table->string('event_type');
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->boolean('resolved')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index(['severity', 'resolved']);
        });

        // ============================================================
        // 8. ATTENDANCE SESSION CHANGES (Change Engine)
        // ============================================================
        Schema::create('attendance_session_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->enum('change_type', ['room', 'time', 'target', 'method', 'cancel', 'extend', 'delay', 'other']);
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

public function down(): void
{
    Schema::dropIfExists('attendance_session_changes');
    Schema::dropIfExists('attendance_security_events');
    Schema::dropIfExists('attendance_audit_logs');
    Schema::dropIfExists('attendance_point_ledgers');
    Schema::dropIfExists('attendance_records');       // ✅ dipindah ke sini
    Schema::dropIfExists('attendance_permissions');    // ✅ setelah records
    Schema::dropIfExists('attendance_targets');
    Schema::dropIfExists('attendance_sessions');
}
};