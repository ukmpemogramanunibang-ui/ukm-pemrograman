<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_health_checks', function (Blueprint $table) {
            $table->id();
            $table->string('status'); // normal, warning, error
            $table->integer('total_checks')->default(0);
            $table->integer('passed_checks')->default(0);
            $table->integer('warning_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->timestamp('checked_at')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->json('details')->nullable(); // optional: simpan detail komponen
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_health_checks');
    }
};