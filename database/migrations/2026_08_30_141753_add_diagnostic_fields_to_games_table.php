<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('diagnostic_status')->default('pending')->after('status');
            $table->json('diagnostic_errors')->nullable()->after('diagnostic_status');
            $table->timestamp('diagnostic_last_run')->nullable()->after('diagnostic_errors');
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['diagnostic_status', 'diagnostic_errors', 'diagnostic_last_run']);
        });
    }
};