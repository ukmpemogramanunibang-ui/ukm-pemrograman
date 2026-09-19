<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom slug (nullable dulu, karena tabel mungkin sudah berisi data)
        Schema::table('competitions', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // 2. Backfill slug untuk data yang sudah ada
        DB::table('competitions')->whereNull('slug')->orderBy('id')->each(function ($row) {
            DB::table('competitions')
                ->where('id', $row->id)
                ->update([
                    'slug' => Str::slug($row->name) . '-' . $row->id,
                ]);
        });

        // 3. Setelah terisi semua, baru pasang unique index
        Schema::table('competitions', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};