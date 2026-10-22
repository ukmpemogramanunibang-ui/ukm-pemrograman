<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom existing yang perlu kita pastikan ada
        $existing = Schema::getColumnListing('group_announcements');

        Schema::table('group_announcements', function (Blueprint $table) use ($existing) {

            // === Base columns (kalau belum ada) ===
            if (! in_array('created_by', $existing)) {
                $table->unsignedBigInteger('created_by')->nullable()->after('id');
            }
            if (! in_array('title', $existing)) {
                $table->string('title')->after('created_by');
            }
            if (! in_array('content', $existing)) {
                $table->text('content')->after('title');
            }
            if (! in_array('is_important', $existing)) {
                $table->boolean('is_important')->default(false)->after('content');
            }
            if (! in_array('status', $existing)) {
                $table->string('status', 30)->default('draft')->after('is_important');
            }
            if (! in_array('published_at', $existing)) {
                $table->timestamp('published_at')->nullable()->after('status');
            }

            // === Enhanced columns ===
            if (! in_array('type', $existing)) {
                $table->string('type', 50)->default('informasi')->after('content');
            }
            if (! in_array('priority', $existing)) {
                $table->enum('priority', ['normal', 'important', 'high', 'urgent'])
                      ->default('normal')->after('type');
            }
            if (! in_array('is_pinned', $existing)) {
                $table->boolean('is_pinned')->default(false)->after('is_important');
            }
            if (! in_array('require_acknowledgement', $existing)) {
                $table->boolean('require_acknowledgement')->default(false)->after('is_pinned');
            }
            if (! in_array('scheduled_at', $existing)) {
                $table->timestamp('scheduled_at')->nullable()->after('require_acknowledgement');
            }
            if (! in_array('expires_at', $existing)) {
                $table->timestamp('expires_at')->nullable()->after('scheduled_at');
            }
            if (! in_array('action_url', $existing)) {
                $table->string('action_url', 500)->nullable()->after('expires_at');
            }
            if (! in_array('attachment_path', $existing)) {
                $table->string('attachment_path', 500)->nullable()->after('action_url');
            }
            if (! in_array('recipient_count', $existing)) {
                $table->unsignedInteger('recipient_count')->default(0)->after('attachment_path');
            }
            if (! in_array('read_count', $existing)) {
                $table->unsignedInteger('read_count')->default(0)->after('recipient_count');
            }
            if (! in_array('ack_count', $existing)) {
                $table->unsignedInteger('ack_count')->default(0)->after('read_count');
            }

            // Index untuk performa
            $table->index(['status', 'scheduled_at'], 'idx_status_scheduled');
            $table->index(['status', 'expires_at'],   'idx_status_expires');
            $table->index('is_pinned',                'idx_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('group_announcements', function (Blueprint $table) {
            $table->dropIndex('idx_status_scheduled');
            $table->dropIndex('idx_status_expires');
            $table->dropIndex('idx_pinned');

            $table->dropColumn([
                'type', 'priority', 'is_pinned', 'require_acknowledgement',
                'scheduled_at', 'expires_at', 'action_url', 'attachment_path',
                'recipient_count', 'read_count', 'ack_count',
            ]);
        });
    }
};