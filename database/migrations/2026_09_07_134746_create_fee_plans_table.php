<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generation_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->bigInteger('amount'); // dalam rupiah
            $table->enum('frequency', ['monthly', 'biweekly', 'weekly'])->default('monthly');
            $table->tinyInteger('due_day')->default(28);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_plans');
    }
};