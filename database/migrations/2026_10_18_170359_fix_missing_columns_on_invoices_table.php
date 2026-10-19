<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'title')) {
                $table->string('title')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('invoices', 'type')) {
                $table->string('type')->default('manual')->after('amount');
            }
            if (!Schema::hasColumn('invoices', 'period')) {
                $table->string('period')->nullable()->after('type');
            }
            if (!Schema::hasColumn('invoices', 'standard_due_date')) {
                $table->date('standard_due_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'adjusted_due_date')) {
                $table->date('adjusted_due_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'final_due_date')) {
                $table->date('final_due_date')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'due_date_reason')) {
                $table->text('due_date_reason')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'penalty')) {
                $table->decimal('penalty', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'discount')) {
                $table->decimal('discount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'total')) {
                $table->decimal('total', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('invoices', 'meta')) {
                $table->json('meta')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'generation_id')) {
                $table->foreignId('generation_id')->nullable()->constrained('generations')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'fee_plan_id')) {
                $table->foreignId('fee_plan_id')->nullable()->constrained('fee_plans')->nullOnDelete();
            }
            if (!Schema::hasColumn('invoices', 'financial_campaign_id')) {
                $table->foreignId('financial_campaign_id')->nullable()->constrained('financial_campaigns')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // Biarkan kosong — kolom ini bagian dari skema inti, tidak aman di-drop otomatis.
    }
};