<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            // Cek dulu supaya tidak error kalau kolom sudah ada
            if (! Schema::hasColumn('login_attempts', 'note')) {
                $table->string('note')->nullable()->after('success');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('login_attempts', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};