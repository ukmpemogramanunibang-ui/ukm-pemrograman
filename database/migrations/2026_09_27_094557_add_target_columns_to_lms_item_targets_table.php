<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Tambah kolom (idempotent)
        Schema::table('lms_item_targets', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_item_targets', 'target_type')) {
                $table->string('target_type', 20)->nullable()->after('item_id');
            }
            if (! Schema::hasColumn('lms_item_targets', 'target_id')) {
                $table->unsignedBigInteger('target_id')->nullable()->after('target_type');
            }
        });

        // 2) Index — pakai raw SQL, cek dulu apakah sudah ada
        try {
            $exists = collect(DB::select("SHOW INDEX FROM lms_item_targets"))
                ->contains(fn ($i) => $i->Key_name === 'lms_item_targets_item_type_idx');

            if (! $exists) {
                DB::statement('CREATE INDEX lms_item_targets_item_type_idx ON lms_item_targets (item_id, target_type)');
            }
        } catch (\Throwable $e) {
            // index gagal dibuat → tidak fatal, lanjut
        }
    }

    public function down(): void
    {
        try {
            DB::statement('DROP INDEX lms_item_targets_item_type_idx ON lms_item_targets');
        } catch (\Throwable $e) { /* ignore */ }

        Schema::table('lms_item_targets', function (Blueprint $table) {
            if (Schema::hasColumn('lms_item_targets', 'target_id')) {
                $table->dropColumn('target_id');
            }
            if (Schema::hasColumn('lms_item_targets', 'target_type')) {
                $table->dropColumn('target_type');
            }
        });
    }
};