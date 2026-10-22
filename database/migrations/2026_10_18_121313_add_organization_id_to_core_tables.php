<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar tabel yang perlu ditambah `organization_id`.
     * ⚠️  SESUAIKAN dengan daftar di file asli Anda.
     */
    private array $tables = [
        'attendance_sessions',
        'attendance_records',
        'announcements',
        'invoices',
        'invoice_items',
        'lms_groups',
        'lms_items',
        // ... tambahkan semua tabel lain dari file asli ...
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            // 🛡️ Guard 1: tabel harus ada
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            // 🛡️ Guard 2: kolom belum ada (INI YANG HILANG DI FILE ANDA)
            if (Schema::hasColumn($tableName, 'organization_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $t) {
                $t->foreignId('organization_id')
                  ->nullable()
                  ->after('id')
                  ->index();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }
            if (! Schema::hasColumn($tableName, 'organization_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $t) {
                try { $t->dropForeign(['organization_id']); } catch (\Throwable $e) {}
                $t->dropColumn('organization_id');
            });
        }
    }
};