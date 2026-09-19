<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('games', function (Blueprint $table) {
        $table->json('cover_images')->nullable()->after('description');
    });
}

public function down()
{
    Schema::table('games', function (Blueprint $table) {
        $table->dropColumn('cover_images');
    });
}
};
