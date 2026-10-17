<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('decision_wheels', function (Blueprint $table) {
        if (! Schema::hasColumn('decision_wheels', 'result_template')) {
            $table->string('result_template', 160)
                  ->default('Sukses kepada {name}!')
                  ->after('title');
        }
    });
}

public function down(): void
{
    Schema::table('decision_wheels', function (Blueprint $table) {
        if (Schema::hasColumn('decision_wheels', 'result_template')) {
            $table->dropColumn('result_template');
        }
    });
}
};
