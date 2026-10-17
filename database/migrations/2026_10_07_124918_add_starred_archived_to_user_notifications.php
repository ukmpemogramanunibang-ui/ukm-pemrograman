<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('user_notifications', 'starred_at')) {
                $table->timestamp('starred_at')->nullable()->after('read_at');
            }
            if (!Schema::hasColumn('user_notifications', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('starred_at');
            }
            if (!Schema::hasColumn('user_notifications', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('category');
            }
            if (!Schema::hasColumn('user_notifications', 'source')) {
                $table->string('source', 50)->default('system')->after('priority');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn(['starred_at', 'archived_at', 'priority', 'source']);
        });
    }
};