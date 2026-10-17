<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance_sessions', 'room_schedule_id')) {
                $table->unsignedBigInteger('room_schedule_id')->nullable()->after('id');
                $table->index('room_schedule_id');
            }
            if (! Schema::hasColumn('attendance_sessions', 'audience_type')) {
                $table->string('audience_type', 30)->nullable()->after('room_schedule_id');
            }
            if (! Schema::hasColumn('attendance_sessions', 'audience_ids')) {
                $table->json('audience_ids')->nullable()->after('audience_type');
            }
            if (! Schema::hasColumn('attendance_sessions', 'attendance_mode')) {
                $table->string('attendance_mode', 20)->default('digital')->after('audience_ids');
            }
            if (! Schema::hasColumn('attendance_sessions', 'source')) {
                $table->string('source', 20)->default('manual')->after('attendance_mode')
                    ->comment('manual|schedule|import');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            foreach ([
                'room_schedule_id', 'audience_type', 'audience_ids',
                'attendance_mode', 'source',
            ] as $col) {
                if (Schema::hasColumn('attendance_sessions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};