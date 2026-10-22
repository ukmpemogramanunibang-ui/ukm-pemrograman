<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('lms_groups', 'organization_id')) {
            Schema::table('lms_groups', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('id')->index();
            });
        }
        if (!Schema::hasColumn('lms_items', 'organization_id')) {
            Schema::table('lms_items', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('id')->index();
            });
        }

        // Backfill data lama ke UNICODE
        $unicodeId = DB::table('organizations')->where('slug', 'ukm-pemrograman-unicode')->value('id') ?? 1;
        DB::table('lms_groups')->whereNull('organization_id')->update(['organization_id' => $unicodeId]);
        DB::table('lms_items')->whereNull('organization_id')->update(['organization_id' => $unicodeId]);
    }

    public function down(): void
    {
        Schema::table('lms_groups', fn (Blueprint $t) => $t->dropColumn('organization_id'));
        Schema::table('lms_items', fn (Blueprint $t) => $t->dropColumn('organization_id'));
    }
};