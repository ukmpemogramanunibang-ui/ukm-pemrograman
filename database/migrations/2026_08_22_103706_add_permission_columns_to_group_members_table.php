<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_members', function (Blueprint $table) {
            if (!Schema::hasColumn('group_members', 'can_view')) {
                $table->boolean('can_view')->default(true);
            }
            if (!Schema::hasColumn('group_members', 'can_announcement')) {
                $table->boolean('can_announcement')->default(false);
            }
            if (!Schema::hasColumn('group_members', 'can_message')) {
                $table->boolean('can_message')->default(true);
            }
            if (!Schema::hasColumn('group_members', 'can_manage_members')) {
                $table->boolean('can_manage_members')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->dropColumn(['can_view', 'can_announcement', 'can_message', 'can_manage_members']);
        });
    }
};