<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: kolom mungkin sudah dibuat di create_games_table atau migration add_* sebelumnya
        if (! Schema::hasColumn('games', 'cover_images')) {
            Schema::table('games', function (Blueprint $table) {
                $table->json('cover_images')->nullable()->after('description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('games', 'cover_images')) {
            Schema::table('games', function (Blueprint $table) {
                $table->dropColumn('cover_images');
            });
        }
    }
};