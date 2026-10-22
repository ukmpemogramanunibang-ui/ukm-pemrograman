<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Tambahkan kolom responsible_person jika belum ada
            if (!Schema::hasColumn('rooms', 'responsible_person')) {
                $table->string('responsible_person')->nullable()->after('status');
            }
            
            // Tambahkan kolom responsible_phone jika belum ada
            if (!Schema::hasColumn('rooms', 'responsible_phone')) {
                $table->string('responsible_phone')->nullable()->after('responsible_person');
            }
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['responsible_person', 'responsible_phone']);
        });
    }
};