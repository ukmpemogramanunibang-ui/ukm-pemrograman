<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $t) {
            if (!Schema::hasColumn('notifications', 'related_id')) {
                $t->string('related_id')->nullable()->after('data');
            }
            if (!Schema::hasColumn('notifications', 'related_type')) {
                $t->string('related_type')->nullable()->after('related_id');
            }
            if (!Schema::hasColumn('notifications', 'action_url')) {
                $t->string('action_url')->nullable()->after('related_type');
            }
            if (!Schema::hasColumn('notifications', 'priority')) {
                $t->string('priority', 20)->default('normal')->after('action_url');
            }
            if (!Schema::hasColumn('notifications', 'category')) {
                $t->string('category', 40)->nullable()->after('priority');
            }
            if (!Schema::hasColumn('notifications', 'starred_at')) {
                $t->timestamp('starred_at')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('notifications', 'archived_at')) {
                $t->timestamp('archived_at')->nullable()->after('starred_at');
            }
        });

        // Index untuk query cepat
        try {
            Schema::table('notifications', function (Blueprint $t) {
                $t->index(['user_id', 'read_at'], 'notif_user_read_idx');
                $t->index(['related_type', 'related_id'], 'notif_related_idx');
                $t->index(['category', 'priority'], 'notif_cat_prio_idx');
            });
        } catch (\Throwable $e) { /* index sudah ada */ }
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $t) {
            foreach (['related_id','related_type','action_url','priority','category','starred_at','archived_at'] as $col) {
                if (Schema::hasColumn('notifications', $col)) $t->dropColumn($col);
            }
        });
    }
};
