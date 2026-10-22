<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
        public function up(): void
    {
        // === AUTO-FIX: idempotent (aman dijalankan berulang) ===
        Schema::table('notifications', function (Blueprint $t) {
            if (!Schema::hasColumn('notifications', 'type')) {
                $t->string('type')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'source')) {
                $t->string('source')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'priority')) {
                $t->string('priority', 20)->default('normal');
            }
            if (!Schema::hasColumn('notifications', 'category')) {
                $t->string('category', 40)->nullable();
            }
            if (!Schema::hasColumn('notifications', 'related_id')) {
                $t->string('related_id')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'related_type')) {
                $t->string('related_type')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'action_url')) {
                $t->string('action_url')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'starred_at')) {
                $t->timestamp('starred_at')->nullable();
            }
            if (!Schema::hasColumn('notifications', 'archived_at')) {
                $t->timestamp('archived_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn([
                'source', 'type_slug', 'priority', 'severity', 'title', 'icon',
                'action_url', 'actor_id', 'entity_type', 'entity_id', 'group_key',
                'requires_action', 'seen_at', 'actioned_at', 'archived_at',
                'starred_at', 'dedup_key',
            ]);
        });
    }
};

