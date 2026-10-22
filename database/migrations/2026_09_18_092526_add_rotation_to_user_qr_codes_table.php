<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_qr_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('user_qr_codes', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('token');
            }
            if (!Schema::hasColumn('user_qr_codes', 'replaces_id')) {
                $table->unsignedBigInteger('replaces_id')->nullable()->after('version');
            }
            if (!Schema::hasColumn('user_qr_codes', 'replaced_by_id')) {
                $table->unsignedBigInteger('replaced_by_id')->nullable()->after('replaces_id');
            }
            if (!Schema::hasColumn('user_qr_codes', 'reason_revoked')) {
                $table->string('reason_revoked', 100)->nullable()->after('revoked_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_qr_codes', function (Blueprint $table) {
            $table->dropColumn(['version', 'replaces_id', 'replaced_by_id', 'reason_revoked']);
        });
    }
};