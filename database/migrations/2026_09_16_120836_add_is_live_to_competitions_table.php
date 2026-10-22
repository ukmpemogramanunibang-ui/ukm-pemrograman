<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🛡️ Guard: skip jika kolom sudah ada (dibuat di create_competitions_table)
        if (Schema::hasColumn('competitions', 'is_live')) {
            return;
        }

        Schema::table('competitions', function (Blueprint $table) {
            $table->boolean('is_live')->default(false)->after('status');
            $table->index('is_live');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('competitions', 'is_live')) {
            return;
        }

        Schema::table('competitions', function (Blueprint $table) {
            try { $table->dropIndex(['is_live']); } catch (\Throwable $e) {}
            $table->dropColumn('is_live');
        });
    }
};