<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_plan_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_plan_id')->constrained()->onDelete('cascade');
            $table->string('version_name');
            $table->bigInteger('amount');
            $table->enum('frequency', ['monthly', 'biweekly', 'weekly']);
            $table->tinyInteger('due_day');
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->text('change_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_plan_versions');
    }
};