<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('lms_groups', 'code')) {
            return; // sudah ada
        }

        Schema::table('lms_groups', function (Blueprint $table) {
            $table->string('code', 50)
                  ->nullable()
                  ->after('id')
                  ->comment('Kode unik pengelompokan');
        });

        // Backfill kode untuk data yang sudah ada
        DB::table('lms_groups')
            ->whereNull('code')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('lms_groups')
                        ->where('id', $row->id)
                        ->update([
                            'code' => 'LMS-' . strtoupper(Str::random(6)),
                        ]);
                }
            });

        // Tambah unique index setelah backfill
        try {
            Schema::table('lms_groups', function (Blueprint $table) {
                $table->unique('code', 'idx_lms_groups_code');
            });
        } catch (\Throwable $e) {
            // index sudah ada → skip
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('lms_groups', 'code')) {
            return;
        }

        Schema::table('lms_groups', function (Blueprint $table) {
            try {
                $table->dropUnique('idx_lms_groups_code');
            } catch (\Throwable $e) {}

            $table->dropColumn('code');
        });
    }
};