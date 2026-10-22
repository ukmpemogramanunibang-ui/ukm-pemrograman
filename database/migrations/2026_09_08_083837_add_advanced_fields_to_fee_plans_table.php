<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fee_plans')) return;

        $has = fn ($col) => Schema::hasColumn('fee_plans', $col);

        Schema::table('fee_plans', function (Blueprint $table) use ($has) {
            // JENIS & TARGET
            if (! $has('fee_type')) {
                $table->enum('fee_type', ['monthly_cash', 'registration', 'special_contribution', 'penalty', 'activity', 'other'])
                    ->default('monthly_cash');
            }
            if (! $has('target_type')) {
                $table->enum('target_type', ['all_members', 'general_members', 'officials', 'core_officials', 'group', 'individual'])
                    ->default('all_members');
            }
            if (! $has('target_data')) {
                $table->json('target_data')->nullable();
            }

            // ATURAN NOMINAL
            if (! $has('nominal_type')) {
                $table->enum('nominal_type', ['fixed', 'by_position', 'by_member'])->default('fixed');
            }
            if (! $has('nominal_data')) {
                $table->json('nominal_data')->nullable();
            }

            // TOLERANSI & DENDA
            if (! $has('grace_period')) {
                $table->tinyInteger('grace_period')->default(3);
            }
            if (! $has('penalty_enabled')) {
                $table->boolean('penalty_enabled')->default(false);
            }
            if (! $has('penalty_type')) {
                $table->enum('penalty_type', ['fixed', 'percentage'])->nullable();
            }
            if (! $has('penalty_amount')) {
                $table->decimal('penalty_amount', 15, 2)->nullable();
            }

            // FITUR TAMBAHAN
            if (! $has('allow_partial')) {
                $table->boolean('allow_partial')->default(false);
            }
            if (! $has('allow_exemption')) {
                $table->boolean('allow_exemption')->default(false);
            }
            if (! $has('notes')) {
                $table->text('notes')->nullable();
            }

            // AUDIT
            if (! $has('created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! $has('updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // No-op
    }
};