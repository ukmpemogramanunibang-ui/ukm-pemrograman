<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            // Cek apakah kolom integration_level sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('games', 'integration_level')) {
                $table->string('integration_level')->nullable()->after('url');
            }
            // Cek apakah kolom api_key sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('games', 'api_key')) {
                $table->string('api_key')->nullable()->after('integration_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['integration_level', 'api_key']);
        });
    }
};