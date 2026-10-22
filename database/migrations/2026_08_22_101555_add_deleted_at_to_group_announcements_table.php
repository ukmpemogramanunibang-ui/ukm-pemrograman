<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: kolom bisa saja sudah dibuat oleh create_group_announcements_table
        if (Schema::hasTable('group_announcements') && ! Schema::hasColumn('group_announcements', 'deleted_at')) {
            Schema::table('group_announcements', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('group_announcements') && Schema::hasColumn('group_announcements', 'deleted_at')) {
            Schema::table('group_announcements', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};