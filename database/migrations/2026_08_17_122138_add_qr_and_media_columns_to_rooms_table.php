<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Tambahkan kolom jika belum ada
            if (!Schema::hasColumn('rooms', 'has_qr')) {
                $table->boolean('has_qr')->default(false);
            }
            if (!Schema::hasColumn('rooms', 'qr_code')) {
                $table->string('qr_code')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'images')) {
                $table->json('images')->nullable();
            }
            if (!Schema::hasColumn('rooms', 'documents')) {
                $table->json('documents')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['has_qr', 'qr_code', 'images', 'documents']);
        });
    }
};