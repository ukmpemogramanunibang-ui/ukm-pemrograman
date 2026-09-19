<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            if (!Schema::hasColumn('generations', 'start_date')) {
                $table->date('start_date')->nullable()->after('entry_year');
            }
            if (!Schema::hasColumn('generations', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('generations', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('generations', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'description', 'slug']);
        });
    }
};