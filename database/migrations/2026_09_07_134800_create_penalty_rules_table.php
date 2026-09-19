<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('level');
            $table->tinyInteger('start_day');
            $table->tinyInteger('end_day')->nullable();
            $table->bigInteger('fixed_amount')->default(0);
            $table->bigInteger('daily_amount')->default(0);
            $table->bigInteger('max_amount')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_rules');
    }
};