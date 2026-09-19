<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            // === JENIS & TARGET ===
            $table->enum('fee_type', [
                'monthly_cash', 'registration', 'special_contribution', 
                'penalty', 'activity', 'other'
            ])->default('monthly_cash')->after('name');

            $table->enum('target_type', [
                'all_members', 'general_members', 'officials', 
                'core_officials', 'group', 'individual'
            ])->default('all_members')->after('fee_type');

            $table->json('target_data')->nullable()->after('target_type'); // menyimpan group_id atau user_id

            // === ATURAN NOMINAL ===
            $table->enum('nominal_type', ['fixed', 'by_position', 'by_member'])->default('fixed')->after('amount');
            $table->json('nominal_data')->nullable()->after('nominal_type'); // menyimpan jabatan->nominal atau anggota->nominal

            // === TOLERANSI & DENDA ===
            $table->tinyInteger('grace_period')->default(3)->after('due_day'); // hari toleransi
            $table->boolean('penalty_enabled')->default(false)->after('grace_period');
            $table->enum('penalty_type', ['fixed', 'percentage'])->nullable()->after('penalty_enabled');
            $table->decimal('penalty_amount', 15, 2)->nullable()->after('penalty_type'); // nominal atau persentase

            // === FITUR TAMBAHAN ===
            $table->boolean('allow_partial')->default(false)->after('penalty_amount');
            $table->boolean('allow_exemption')->default(false)->after('allow_partial');
            $table->text('notes')->nullable()->after('allow_exemption');

            // === AUDIT ===
            $table->foreignId('created_by')->nullable()->constrained('users')->after('notes');
            $table->foreignId('updated_by')->nullable()->constrained('users')->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            $table->dropColumn([
                'fee_type', 'target_type', 'target_data',
                'nominal_type', 'nominal_data',
                'grace_period', 'penalty_enabled', 'penalty_type', 'penalty_amount',
                'allow_partial', 'allow_exemption', 'notes',
                'created_by', 'updated_by'
            ]);
        });
    }
};