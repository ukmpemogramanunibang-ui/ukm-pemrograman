<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: kolom mungkin sudah dibuat di create_divisions_table
        if (! Schema::hasColumn('divisions', 'ketua_label')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->string('ketua_label')->default('Ketua');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('divisions', 'ketua_label')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->dropColumn('ketua_label');
            });
        }
    }
};