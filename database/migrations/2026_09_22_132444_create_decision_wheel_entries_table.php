<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('decision_wheel_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wheel_id')->constrained('decision_wheels')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('weight')->default(0); // 0-100
            $table->unsignedSmallInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['wheel_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_wheel_entries');
    }
};