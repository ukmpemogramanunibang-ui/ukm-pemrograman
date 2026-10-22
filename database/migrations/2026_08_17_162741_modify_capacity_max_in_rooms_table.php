<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Ubah kolom capacity_max agar nullable dengan default 0
            $table->integer('capacity_max')->nullable()->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('capacity_max')->default(0)->change();
        });
    }
};