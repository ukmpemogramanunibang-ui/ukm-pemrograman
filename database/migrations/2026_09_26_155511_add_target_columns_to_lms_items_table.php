<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_items', function (Blueprint $table) {

            // ---- Target Mode ----
            if (! Schema::hasColumn('lms_items', 'target_mode')) {
                $table->string('target_mode', 20)
                      ->default('role')
                      ->after('video_provider')
                      ->comment('role | individual | both');
            }

            // ---- Target Roles (JSON) ----
            if (! Schema::hasColumn('lms_items', 'target_roles')) {
                $table->json('target_roles')
                      ->nullable()
                      ->after('target_mode')
                      ->comment('Array of level_id: [3,4,5]');
            }

            // ---- Target Users (JSON) ----
            if (! Schema::hasColumn('lms_items', 'target_user_ids')) {
                $table->json('target_user_ids')
                      ->nullable()
                      ->after('target_roles')
                      ->comment('Array of user_id: [12,15,18]');
            }

            // ---- Recipients Count ----
            if (! Schema::hasColumn('lms_items', 'recipients_count')) {
                $table->unsignedInteger('recipients_count')
                      ->default(0)
                      ->after('target_user_ids');
            }

            if (! Schema::hasColumn('lms_items', 'recipient_count')) {
                $table->unsignedInteger('recipient_count')
                      ->default(0)
                      ->after('recipients_count');
            }

            // ---- Visibility ----
            if (! Schema::hasColumn('lms_items', 'visibility')) {
                $table->string('visibility', 20)
                      ->default('public')
                      ->after('status');
            }

            // ---- Indexes untuk performa query ----
            $this->addIndexIfMissing('lms_items', ['status', 'type'], 'idx_lms_status_type');
            $this->addIndexIfMissing('lms_items', ['created_by'], 'idx_lms_creator');
            $this->addIndexIfMissing('lms_items', ['group_id'], 'idx_lms_group');
            $this->addIndexIfMissing('lms_items', ['published_at'], 'idx_lms_published');
        });

        // ---- Index di notifications untuk query cepat ----
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (! $this->hasIndex('notifications', 'idx_notif_entity')) {
                    try {
                        $table->index(['entity_type', 'entity_id'], 'idx_notif_entity');
                    } catch (\Throwable $e) {}
                }
                if (! $this->hasIndex('notifications', 'idx_notif_user_unread')) {
                    try {
                        $table->index(['user_id', 'read_at'], 'idx_notif_user_unread');
                    } catch (\Throwable $e) {}
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('lms_items', function (Blueprint $table) {
            $columns = [
                'target_mode', 'target_roles', 'target_user_ids',
                'recipients_count', 'recipient_count', 'visibility',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('lms_items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /* =========================================================
     | HELPERS
     * ========================================================= */

    protected function addIndexIfMissing(string $table, array $columns, string $name): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                $t->index($columns, $name);
            });
        } catch (\Throwable $e) {
            // Index sudah ada — skip
        }
    }

    protected function hasIndex(string $table, string $indexName): bool
    {
        try {
            $indexes = \DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return ! empty($indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }
};