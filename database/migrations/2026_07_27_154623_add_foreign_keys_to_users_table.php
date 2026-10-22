<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('university_id')
                ->references('id')
                ->on('universities')
                ->nullOnDelete();

            $table->foreign('major_id')
                ->references('id')
                ->on('majors')
                ->nullOnDelete();

            $table->foreign('level_id')
                ->references('id')
                ->on('levels')
                ->restrictOnDelete();

            $table->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropForeign(['major_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['status_id']);
        });
    }
};