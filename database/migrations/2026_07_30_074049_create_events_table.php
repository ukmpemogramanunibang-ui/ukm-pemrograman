<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->string('status')->default('aktif'); // kolom status
                $table->unsignedSmallInteger('auto_delete_after_days')->nullable();
                $table->timestamps();
        });
    }
    }
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};