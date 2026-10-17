<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_items', function (Blueprint $table) {
            if (! Schema::hasColumn('lms_items', 'cover_path')) {
                $table->string('cover_path')->nullable()->after('description');
            }
            if (! Schema::hasColumn('lms_items', 'cover_images')) {
                $table->json('cover_images')->nullable()->after('cover_path');
            }
            if (! Schema::hasColumn('lms_items', 'cover_meta')) {
                $table->json('cover_meta')->nullable()->after('cover_images');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lms_items', function (Blueprint $table) {
            $table->dropColumn(['cover_path', 'cover_images', 'cover_meta']);
        });
    }
};