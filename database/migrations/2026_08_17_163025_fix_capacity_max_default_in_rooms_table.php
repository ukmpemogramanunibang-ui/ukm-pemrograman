<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Pastikan capacity_max nullable dengan default 0
            if (Schema::hasColumn('rooms', 'capacity_max')) {
                $table->integer('capacity_max')->nullable()->default(0)->change();
            }
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'capacity_max')) {
                $table->integer('capacity_max')->default(0)->change();
            }
        });
    }
};