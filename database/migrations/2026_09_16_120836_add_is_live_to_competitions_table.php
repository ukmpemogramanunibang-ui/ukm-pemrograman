<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            // taruh setelah kolom 'status' agar rapi
            $table->boolean('is_live')
                  ->default(false)
                  ->after('status')
                  ->index(); // untuk query where('is_live', true) yang cepat
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropIndex(['is_live']);
            $table->dropColumn('is_live');
        });
    }
};