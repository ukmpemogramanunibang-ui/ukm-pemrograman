<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan generation_id jika belum ada
            if (!Schema::hasColumn('users', 'generation_id')) {
                $table->foreignId('generation_id')->nullable()->constrained('generations')->nullOnDelete();
            }

            // Tambahkan member_number jika belum ada
            if (!Schema::hasColumn('users', 'member_number')) {
                $table->string('member_number')->nullable()->unique();
            }

            // Tambahkan membership_status jika belum ada
            if (!Schema::hasColumn('users', 'membership_status')) {
                $table->enum('membership_status', ['active', 'suspended', 'blacklisted', 'resigned', 'reactivation_pending'])->default('active');
            }

            // Tambahkan suspended_at jika belum ada
            if (!Schema::hasColumn('users', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable();
            }

            // Tambahkan blacklisted_at jika belum ada
            if (!Schema::hasColumn('users', 'blacklisted_at')) {
                $table->timestamp('blacklisted_at')->nullable();
            }

            // Tambahkan membership_notes jika belum ada
            if (!Schema::hasColumn('users', 'membership_notes')) {
                $table->text('membership_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom hanya jika ada
            if (Schema::hasColumn('users', 'generation_id')) {
                $table->dropConstrainedForeignId('generation_id');
            }
            if (Schema::hasColumn('users', 'member_number')) {
                $table->dropColumn('member_number');
            }
            if (Schema::hasColumn('users', 'membership_status')) {
                $table->dropColumn('membership_status');
            }
            if (Schema::hasColumn('users', 'suspended_at')) {
                $table->dropColumn('suspended_at');
            }
            if (Schema::hasColumn('users', 'blacklisted_at')) {
                $table->dropColumn('blacklisted_at');
            }
            if (Schema::hasColumn('users', 'membership_notes')) {
                $table->dropColumn('membership_notes');
            }
        });
    }
};