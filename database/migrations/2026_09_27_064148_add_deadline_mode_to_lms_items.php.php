<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_items', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_items', 'deadline_mode')) {
                $table->string('deadline_mode', 20)
                    ->default('limited')
                    ->after('deadline_at');
                // limited | unlimited
            }

            if (! Schema::hasColumn('lms_items', 'attachment_meta')) {
                $table->json('attachment_meta')->nullable()->after('attachments');
                // menyimpan nama asli, size, mime, dsb.
            }
        });
    }

    public function down(): void
    {
        Schema::table('lms_items', function (Blueprint $table) {
            $table->dropColumn(['deadline_mode', 'attachment_meta']);
        });
    }
};