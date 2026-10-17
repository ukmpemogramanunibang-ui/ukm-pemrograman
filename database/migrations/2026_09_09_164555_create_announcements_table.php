<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {

            /* ── Kolom tambahan (aman, tidak ganggu data lama) ── */

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

        /* ── Index untuk performa ── */
        Schema::table('announcements', function (Blueprint $table) {
            try { $table->index(['created_by', 'created_at'], 'ann_creator_idx'); } catch (\Throwable $e) {}
            try { $table->index('priority', 'ann_priority_idx'); } catch (\Throwable $e) {}
            try { $table->index('published_at', 'ann_published_idx'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            foreach (['priority', 'target_roles', 'action_url', 'read_count', 'updated_by', 'deleted_at'] as $col) {
                if (Schema::hasColumn('announcements', $col)) {
                    if (in_array($col, ['updated_by'], true)) {
                        try { $table->dropForeign([$col]); } catch (\Throwable $e) {}
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};