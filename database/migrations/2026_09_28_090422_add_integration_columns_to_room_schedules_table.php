<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            // ── Audience (target penerima)
            if (! Schema::hasColumn('room_schedules', 'audience_type')) {
                $table->string('audience_type', 30)
                    ->default('all')
                    ->after('activity_type')
                    ->comment('all|kelompok|generasi|pengurus|lms_group|role');
            }
            if (! Schema::hasColumn('room_schedules', 'audience_ids')) {
                $table->json('audience_ids')->nullable()->after('audience_type');
            }
            if (! Schema::hasColumn('room_schedules', 'audience_count')) {
                $table->unsignedInteger('audience_count')->default(0)->after('audience_ids');
            }

            // ── Attendance mode
            if (! Schema::hasColumn('room_schedules', 'attendance_mode')) {
                $table->string('attendance_mode', 20)
                    ->default('none')
                    ->after('audience_count')
                    ->comment('none|digital|upload');
            }
            if (! Schema::hasColumn('room_schedules', 'attendance_target')) {
                $table->unsignedInteger('attendance_target')->default(0)->after('attendance_mode');
            }
            if (! Schema::hasColumn('room_schedules', 'auto_create_attendance')) {
                $table->boolean('auto_create_attendance')->default(false)->after('attendance_target');
            }
            if (! Schema::hasColumn('room_schedules', 'requires_review')) {
                $table->boolean('requires_review')->default(true)->after('auto_create_attendance');
            }

            // ── Link
            if (! Schema::hasColumn('room_schedules', 'attendance_session_id')) {
                $table->unsignedBigInteger('attendance_session_id')->nullable()->after('requires_review');
            }
            if (! Schema::hasColumn('room_schedules', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('attendance_session_id');
            }

            $table->index(['audience_type', 'attendance_mode'], 'rs_audience_att_idx');
        });
    }

    public function down(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            foreach ([
                'audience_type', 'audience_ids', 'audience_count',
                'attendance_mode', 'attendance_target', 'auto_create_attendance',
                'requires_review', 'attendance_session_id', 'created_by',
            ] as $col) {
                if (Schema::hasColumn('room_schedules', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};