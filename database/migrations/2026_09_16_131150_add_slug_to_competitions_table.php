<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🛡️ Guard: skip jika slug sudah ada
        if (Schema::hasColumn('competitions', 'slug')) {
            return;
        }

        Schema::table('competitions', function (Blueprint $table) {
            $table->string('slug', 150)->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('competitions', 'slug')) {
            return;
        }

        Schema::table('competitions', function (Blueprint $table) {
            try { $table->dropUnique(['slug']); } catch (\Throwable $e) {}
            $table->dropColumn('slug');
        });
    }
};