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
            // ── Pembuat invoice (audit trail) ──
            if (! Schema::hasColumn('invoices', 'created_by')) {
                $t->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            }

            // ── Pengubah terakhir invoice ──
            if (! Schema::hasColumn('invoices', 'updated_by')) {
                $t->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $t) {
            foreach (['created_by', 'updated_by'] as $col) {
                if (Schema::hasColumn('invoices', $col)) {
                    try { $t->dropForeign([$col]); } catch (\Throwable $e) {}
                }
            }

            $cols = array_values(array_filter(
                ['created_by', 'updated_by'],
                fn ($c) => Schema::hasColumn('invoices', $c)
            ));

            if (! empty($cols)) {
                $t->dropColumn($cols);
            }
        });
    }
};