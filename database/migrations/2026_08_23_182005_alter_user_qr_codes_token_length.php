<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_qr_codes', function (Blueprint $table) {
            $table->string('token', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_qr_codes', function (Blueprint $table) {
            $table->string('token', 120)->nullable()->change();
        });
    }
};