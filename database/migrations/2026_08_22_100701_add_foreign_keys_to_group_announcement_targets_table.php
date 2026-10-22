<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_announcement_targets', function (Blueprint $table) {
            // Cek dan tambahkan kolom jika belum ada
            if (!Schema::hasColumn('group_announcement_targets', 'announcement_id')) {
                $table->foreignId('announcement_id')->constrained('group_announcements')->onDelete('cascade');
            }
            if (!Schema::hasColumn('group_announcement_targets', 'group_id')) {
                $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            }
            if (!Schema::hasColumn('group_announcement_targets', 'is_read')) {
                $table->boolean('is_read')->default(false);
            }
            if (!Schema::hasColumn('group_announcement_targets', 'read_at')) {
                $table->timestamp('read_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_announcement_targets', function (Blueprint $table) {
            $table->dropForeign(['announcement_id']);
            $table->dropForeign(['group_id']);
            $table->dropColumn(['announcement_id', 'group_id', 'is_read', 'read_at']);
        });
    }
};