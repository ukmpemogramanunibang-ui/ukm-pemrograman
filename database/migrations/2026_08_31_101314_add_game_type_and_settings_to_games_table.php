<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('games', 'type')) {
                $table->string('type')->nullable()->after('name')->comment('native atau external');
            }
            if (!Schema::hasColumn('games', 'game_type')) {
                $table->string('game_type')->nullable()->after('type')->comment('typing, quiz, coding, dll');
            }
            if (!Schema::hasColumn('games', 'settings')) {
                $table->json('settings')->nullable()->after('game_type');
            }
            if (!Schema::hasColumn('games', 'cover_images')) {
                $table->json('cover_images')->nullable()->after('settings');
            }
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['type', 'game_type', 'settings', 'cover_images']);
        });
    }
};