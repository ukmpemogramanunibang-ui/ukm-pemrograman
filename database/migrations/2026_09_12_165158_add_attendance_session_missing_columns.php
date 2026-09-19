<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            // Cek & tambah kolom hanya jika belum ada
            if (!Schema::hasColumn('attendance_sessions', 'allowed_methods')) {
                $table->json('allowed_methods')->nullable()->after('session_status');
            }
            if (!Schema::hasColumn('attendance_sessions', 'target_type')) {
                $table->string('target_type', 30)->default('all')->after('target_count');
            }
            if (!Schema::hasColumn('attendance_sessions', 'shift_name')) {
                $table->string('shift_name', 100)->nullable()->after('room_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn(['allowed_methods', 'target_type', 'shift_name']);
        });
    }
};