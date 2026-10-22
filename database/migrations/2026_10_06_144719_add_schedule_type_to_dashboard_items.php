<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_items', function (Blueprint $table) {
            if (! Schema::hasColumn('dashboard_items', 'schedule_type')) {
                $table->enum('schedule_type', ['always', 'scheduled', 'expires'])
                    ->default('always')
                    ->after('sort_order')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_items', function (Blueprint $table) {
            if (Schema::hasColumn('dashboard_items', 'schedule_type')) {
                $table->dropColumn('schedule_type');
            }
        });
    }
};