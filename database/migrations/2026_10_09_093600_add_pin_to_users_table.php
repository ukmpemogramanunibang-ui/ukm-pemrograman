<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pin', 255)->nullable()->after('password');
            });
        } else {
            // Kalau sudah ada tapi mungkin bukan string panjang, ubah
            Schema::table('users', function (Blueprint $table) {
                $table->string('pin', 255)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('pin');
            });
        }
    }
};