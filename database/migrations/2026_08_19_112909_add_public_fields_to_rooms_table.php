<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('rooms', function (Blueprint $table) {
        $table->enum('public_status', ['public', 'login', 'both'])->nullable()->after('qr_code');
        $table->timestamp('published_at')->nullable()->after('public_status');
        $table->json('public_roles')->nullable()->after('published_at');
        $table->json('public_fields')->nullable()->after('public_roles');
    });
}

public function down()
{
    Schema::table('rooms', function (Blueprint $table) {
        $table->dropColumn(['public_status', 'published_at', 'public_roles', 'public_fields']);
    });
}
};
