<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom redundan
            foreach (['jabatan', 'level', 'rank', 'uuid', 'user_status'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Hapus foreign key yang sudah ada (jika ada)
            $foreignKeys = [
                'university_id' => 'users_university_id_foreign',
                'major_id' => 'users_major_id_foreign',
                'level_id' => 'users_level_id_foreign',
                'status_id' => 'users_status_id_foreign',
            ];

            foreach ($foreignKeys as $col => $fkName) {
                if (Schema::hasColumn('users', $col)) {
                    try {
                        $table->dropForeign($fkName);
                    } catch (\Exception $e) {
                        // Abaikan jika tidak ada
                    }
                }
            }

            // Pastikan kolom ada
            if (!Schema::hasColumn('users', 'university_id')) {
                $table->unsignedBigInteger('university_id')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'major_id')) {
                $table->unsignedBigInteger('major_id')->nullable()->after('university_id');
            }
            if (!Schema::hasColumn('users', 'level_id')) {
                $table->unsignedBigInteger('level_id')->nullable()->after('major_id');
            }
            if (!Schema::hasColumn('users', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->after('level_id');
            }

            // Tambahkan foreign key baru
            $table->foreign('university_id')->references('id')->on('universities')->onDelete('set null');
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('set null');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['university_id']);
            $table->dropForeign(['major_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['status_id']);
            $table->dropColumn(['university_id', 'major_id', 'level_id', 'status_id']);
        });
    }
};