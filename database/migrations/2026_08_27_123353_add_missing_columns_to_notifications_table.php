<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('notifications', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('type');
            }
            if (!Schema::hasColumn('notifications', 'is_starred')) {
                $table->boolean('is_starred')->default(false)->after('priority');
            }
            if (!Schema::hasColumn('notifications', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('notifications', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('archived_at');
            }
            if (!Schema::hasColumn('notifications', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('notifications', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
            if (!Schema::hasColumn('notifications', 'conversation_id')) {
                $table->foreignId('conversation_id')->nullable()->after('reference_id');
                // Jangan pakai constrained('conversations') jika tabel conversations belum ada
            }
            if (!Schema::hasColumn('notifications', 'action_url')) {
                $table->string('action_url')->nullable()->after('link');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $columns = ['priority', 'is_starred', 'archived_at', 'expires_at', 'reference_type', 'reference_id', 'conversation_id', 'action_url'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('notifications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};