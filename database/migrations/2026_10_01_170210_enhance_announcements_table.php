<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🛡️ Guard: kalau tabel belum ada, skip (tidak error)
        if (! Schema::hasTable('announcements')) {
            return;
        }

        Schema::table('announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('announcements', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('content');
            }
            if (! Schema::hasColumn('announcements', 'target_roles')) {
                $table->json('target_roles')->nullable()->after('priority');
            }
            if (! Schema::hasColumn('announcements', 'action_url')) {
                $table->string('action_url', 500)->nullable()->after('target_roles');
            }
            if (! Schema::hasColumn('announcements', 'read_count')) {
                $table->unsignedInteger('read_count')->default(0)->after('action_url');
            }
            if (! Schema::hasColumn('announcements', 'updated_by')) {
                $table->foreignId('updated_by')
                      ->nullable()
                      ->after('created_by')
                      ->constrained('users')
                      ->nullOnDelete();
            }
            if (! Schema::hasColumn('announcements', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });

        // Index — dibungkus try/catch supaya aman dari duplikat
        Schema::table('announcements', function (Blueprint $table) {
            foreach ([
                ['created_by', 'created_at', 'ann_creator_idx'],
                ['priority', null, 'ann_priority_idx'],
                ['published_at', null, 'ann_published_idx'],
            ] as [$col1, $col2, $name]) {
                try {
                    $col2
                        ? $table->index([$col1, $col2], $name)
                        : $table->index($col1, $name);
                } catch (\Throwable $e) {
                    // index sudah ada — abaikan
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('announcements')) {
            return;
        }

        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'updated_by')) {
                try { $table->dropForeign(['updated_by']); } catch (\Throwable $e) {}
            }

            $cols = array_values(array_filter(
                ['priority', 'target_roles', 'action_url', 'read_count', 'updated_by', 'deleted_at'],
                fn ($col) => Schema::hasColumn('announcements', $col)
            ));

            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};