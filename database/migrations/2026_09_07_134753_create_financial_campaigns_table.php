<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // id_card, event, reunion, merchandise, etc.
            $table->bigInteger('amount');
            $table->boolean('is_required')->default(true);
            $table->enum('target_type', ['all', 'generation', 'specific'])->default('all');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('due_date');
            $table->boolean('allow_installment')->default(false);
            $table->boolean('allow_pre_payment')->default(false);
            $table->bigInteger('max_payment')->nullable();
            $table->enum('status', ['draft', 'active', 'closed', 'cancelled'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_campaigns');
    }
};