<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom mungkin sudah dibuat di create_division_members_table
        if (! Schema::hasColumn('division_members', 'custom_position')) {
            Schema::table('division_members', function (Blueprint $table) {
                $table->string('custom_position')->nullable()->after('position');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('division_members', 'custom_position')) {
            Schema::table('division_members', function (Blueprint $table) {
                $table->dropColumn('custom_position');
            });
        }
    }
};