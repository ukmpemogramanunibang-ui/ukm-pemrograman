<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ukm_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->enum('type', ['activity', 'holiday', 'academic', 'special'])->default('activity');
            $table->text('description')->nullable();
            $table->boolean('is_non_active')->default(false); // true = UKM tidak aktif
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukm_calendars');
    }
};