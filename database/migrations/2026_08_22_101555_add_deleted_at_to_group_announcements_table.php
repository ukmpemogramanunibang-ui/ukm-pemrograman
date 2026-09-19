<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_announcements', function (Blueprint $table) {
            // Cek apakah kolom deleted_at sudah ada
            if (!Schema::hasColumn('group_announcements', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_announcements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};