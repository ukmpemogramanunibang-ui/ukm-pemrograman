<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan ke tabel absensi (nullable dulu agar data lama aman)
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id')->nullable()->after('id')->index();
        });

        // 2. Tambahkan ke tabel tagihan/keuangan
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('organization_id')->nullable()->after('id')->index();
        });

        // 3. Set data lama (existing) ke UNICODE (id = 1) secara otomatis
        // Asumsi ID 1 adalah UNICODE
        $unicodeId = DB::table('organizations')->where('slug', 'ukm-pemrograman-unicode')->value('id') ?? 1;
        
        DB::table('attendance_sessions')->whereNull('organization_id')->update(['organization_id' => $unicodeId]);
        DB::table('invoices')->whereNull('organization_id')->update(['organization_id' => $unicodeId]);
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
};