<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $t) {

            // ═══════════════════════════════════════════════════════════
            // A. IDENTITAS INVOICE
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'title')) {
                $t->string('title', 200)->nullable();
            }
            if (! Schema::hasColumn('invoices', 'description')) {
                $t->text('description')->nullable();
            }
            if (! Schema::hasColumn('invoices', 'notes')) {
                $t->text('notes')->nullable();
            }

            // ═══════════════════════════════════════════════════════════
            // B. KLASIFIKASI & PERIODE
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'type')) {
                $t->string('type', 32)->default('manual');
            }
            if (! Schema::hasColumn('invoices', 'period')) {
                $t->string('period', 32)->nullable();
            }

            // ═══════════════════════════════════════════════════════════
            // C. DUE DATE (standar, disesuaikan, final)
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'standard_due_date')) {
                $t->date('standard_due_date')->nullable();
            }
            if (! Schema::hasColumn('invoices', 'adjusted_due_date')) {
                $t->date('adjusted_due_date')->nullable();
            }
            if (! Schema::hasColumn('invoices', 'final_due_date')) {
                $t->date('final_due_date')->nullable();
            }
            if (! Schema::hasColumn('invoices', 'due_date_reason')) {
                $t->text('due_date_reason')->nullable();
            }

            // ═══════════════════════════════════════════════════════════
            // D. NILAI FINANSIAL (semua decimal 15,2 — KONSISTEN)
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'subtotal')) {
                $t->decimal('subtotal', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('invoices', 'penalty')) {
                $t->decimal('penalty', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('invoices', 'discount')) {
                $t->decimal('discount', 15, 2)->default(0);
            }
            if (! Schema::hasColumn('invoices', 'total')) {
                $t->decimal('total', 15, 2)->default(0);
            }

            // ═══════════════════════════════════════════════════════════
            // E. MATA UANG & METADATA
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'currency')) {
                $t->string('currency', 8)->default('IDR');
            }
            if (! Schema::hasColumn('invoices', 'meta')) {
                $t->json('meta')->nullable();
            }
        });

        // ═══════════════════════════════════════════════════════════════
        // F. FK OPSIONAL — dibungkus Schema::hasTable untuk safety
        //    (tabel referensi dibuat lebih awal di batch 43)
        // ═══════════════════════════════════════════════════════════════
        Schema::table('invoices', function (Blueprint $t) {
            if (! Schema::hasColumn('invoices', 'generation_id') && Schema::hasTable('generations')) {
                $t->foreignId('generation_id')->nullable()
                  ->constrained('generations')->nullOnDelete();
            }
            if (! Schema::hasColumn('invoices', 'fee_plan_id') && Schema::hasTable('fee_plans')) {
                $t->foreignId('fee_plan_id')->nullable()
                  ->constrained('fee_plans')->nullOnDelete();
            }
            if (! Schema::hasColumn('invoices', 'financial_campaign_id') && Schema::hasTable('financial_campaigns')) {
                $t->foreignId('financial_campaign_id')->nullable()
                  ->constrained('financial_campaigns')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $t) {
            // Drop FK dulu, baru kolom
            foreach (['generation_id', 'fee_plan_id', 'financial_campaign_id'] as $fk) {
                if (Schema::hasColumn('invoices', $fk)) {
                    try { $t->dropForeign([$fk]); } catch (\Throwable $e) {}
                }
            }
        });

        Schema::table('invoices', function (Blueprint $t) {
            $columns = [
                'title', 'description', 'notes',
                'type', 'period',
                'standard_due_date', 'adjusted_due_date', 'final_due_date', 'due_date_reason',
                'subtotal', 'penalty', 'discount', 'total',
                'currency', 'meta',
                'generation_id', 'fee_plan_id', 'financial_campaign_id',
            ];

            $existing = array_values(array_filter(
                $columns,
                fn ($c) => Schema::hasColumn('invoices', $c)
            ));

            if (! empty($existing)) {
                $t->dropColumn($existing);
            }
        });
    }
};