<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('generation_id')->nullable()->constrained();
    $table->foreignId('fee_plan_id')->nullable()->constrained();
    $table->foreignId('financial_campaign_id')->nullable()->constrained(); // ✅ nullable
    $table->string('type');
    $table->string('period')->nullable();
    $table->date('standard_due_date');
    $table->date('adjusted_due_date')->nullable();
    $table->date('final_due_date');
    $table->text('due_date_reason')->nullable();
    $table->bigInteger('subtotal');
    $table->bigInteger('penalty')->default(0);
    $table->bigInteger('discount')->default(0);
    $table->bigInteger('total');
    $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled', 'refunded'])->default('pending');
    $table->timestamp('paid_at')->nullable();
    $table->json('meta')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};