<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $existing = Schema::getColumnListing('notifications');

        Schema::table('notifications', function (Blueprint $table) use ($existing) {

            /* ---------- Konten ---------- */
            if (! in_array('title', $existing)) {
                $table->string('title', 255)->nullable()->after('type');
            }
            if (! in_array('message', $existing)) {
                $table->text('message')->nullable()->after('title');
            }
            if (! in_array('icon', $existing)) {
                $table->string('icon', 64)->nullable()->after('message');
            }

            /* ---------- Klasifikasi ---------- */
            if (! in_array('source', $existing)) {
                $table->string('source', 64)->nullable()->index()->after('icon');
            }
            if (! in_array('type_slug', $existing)) {
                $table->string('type_slug', 120)->nullable()->index()->after('source');
            }
            if (! in_array('category', $existing)) {
                $table->string('category', 64)->nullable()->index()->after('type_slug');
            }
            if (! in_array('priority', $existing)) {
                $table->string('priority', 20)->nullable()->default('normal')->index()->after('category');
            }
            if (! in_array('severity', $existing)) {
                $table->string('severity', 20)->nullable()->after('priority');
            }

            /* ---------- Aksi ---------- */
            if (! in_array('action_url', $existing)) {
                $table->string('action_url', 500)->nullable()->after('severity');
            }
            if (! in_array('deep_link', $existing)) {
                $table->string('deep_link', 500)->nullable()->after('action_url');
            }
            if (! in_array('requires_action', $existing)) {
                $table->boolean('requires_action')->default(false)->index()->after('deep_link');
            }
            if (! in_array('actioned_at', $existing)) {
                $table->timestamp('actioned_at')->nullable()->after('requires_action');
            }

            /* ---------- Aktor & Entity ---------- */
            if (! in_array('actor_id', $existing)) {
                $table->unsignedBigInteger('actor_id')->nullable()->index()->after('actioned_at');
            }
            if (! in_array('entity_type', $existing)) {
                $table->string('entity_type', 191)->nullable()->after('actor_id');
            }
            if (! in_array('entity_id', $existing)) {
                $table->unsignedBigInteger('entity_id')->nullable()->index()->after('entity_type');
            }
            if (! in_array('related_type', $existing)) {
                $table->string('related_type', 191)->nullable()->after('entity_id');
            }
            if (! in_array('related_id', $existing)) {
                $table->unsignedBigInteger('related_id')->nullable()->after('related_type');
            }

            /* ---------- Grouping & Dedup ---------- */
            if (! in_array('group_key', $existing)) {
                $table->string('group_key', 120)->nullable()->index()->after('related_id');
            }
            if (! in_array('dedup_key', $existing)) {
                $table->string('dedup_key', 191)->nullable()->index()->after('group_key');
            }

            /* ---------- Status tambahan ---------- */
            if (! in_array('seen_at', $existing)) {
                $table->timestamp('seen_at')->nullable()->index()->after('read_at');
            }
            if (! in_array('starred_at', $existing)) {
                $table->timestamp('starred_at')->nullable()->index()->after('seen_at');
            }
            if (! in_array('archived_at', $existing)) {
                $table->timestamp('archived_at')->nullable()->index()->after('starred_at');
            }

            /* ---------- Event tracking (dipakai di serializeItem) ---------- */
            if (! in_array('event_type', $existing)) {
                $table->string('event_type', 20)->default('created')->after('archived_at');
            }
            if (! in_array('version', $existing)) {
                $table->unsignedInteger('version')->default(1)->after('event_type');
            }
            if (! in_array('source_deleted_at', $existing)) {
                $table->timestamp('source_deleted_at')->nullable()->after('version');
            }
        });

        /* ---------- Index komposit untuk polling ---------- */
        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['user_id', 'archived_at'], 'notif_user_archived_idx');
            });
        } catch (\Throwable $e) { /* sudah ada → skip */ }

        try {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'notif_user_created_idx');
            });
        } catch (\Throwable $e) { /* sudah ada → skip */ }
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            foreach ([
                'title', 'message', 'icon',
                'source', 'type_slug', 'category', 'priority', 'severity',
                'action_url', 'deep_link', 'requires_action', 'actioned_at',
                'actor_id', 'entity_type', 'entity_id', 'related_type', 'related_id',
                'group_key', 'dedup_key',
                'seen_at', 'starred_at', 'archived_at',
                'event_type', 'version', 'source_deleted_at',
            ] as $col) {
                if (Schema::hasColumn('notifications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};