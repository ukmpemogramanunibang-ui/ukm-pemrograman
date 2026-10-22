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
        Schema::table('jabatan_user', function (Blueprint $table) {
             $table->foreign('organizational_period_id')
              ->references('id')
              ->on('organizational_periods')
              ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatan_user', function (Blueprint $table) {
            $table->dropForeign(['organizational_period_id']);
        });
    }
};
