<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Kolom sudah ada di DB, jadi kita hanya tambah yang belum ada
            if (! Schema::hasColumn('notifications', 'related_type')) {
                $table->string('related_type')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'related_id')) {
                $table->unsignedBigInteger('related_id')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'event_type')) {
                $table->string('event_type', 20)->default('created');
            }
            if (! Schema::hasColumn('notifications', 'version')) {
                $table->unsignedInteger('version')->default(1);
            }
            if (! Schema::hasColumn('notifications', 'source_deleted_at')) {
                $table->timestamp('source_deleted_at')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'source_updated_at')) {
                $table->timestamp('source_updated_at')->nullable();
            }
            if (! Schema::hasColumn('notifications', 'fingerprint')) {
                $table->string('fingerprint', 64)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $drop = [];
            foreach ([
                'related_type', 'related_id', 'event_type', 'version',
                'source_deleted_at', 'source_updated_at', 'fingerprint',
            ] as $col) {
                if (Schema::hasColumn('notifications', $col)) {
                    $drop[] = $col;
                }
            }
            if ($drop) $table->dropColumn($drop);
        });
    }
};