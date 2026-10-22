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
            // amount — HARUS decimal(15,2), bukan bigInteger
            // (konsisten dengan subtotal/penalty/discount/total)
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'amount')) {
                $t->decimal('amount', 15, 2)->default(0);
            }

            // ═══════════════════════════════════════════════════════════
            // due_date — tanggal jatuh tempo utama
            // ═══════════════════════════════════════════════════════════
            if (! Schema::hasColumn('invoices', 'due_date')) {
                $t->date('due_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('invoices')) {
            return;
        }

        Schema::table('invoices', function (Blueprint $t) {
            $columns = ['amount', 'due_date'];
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