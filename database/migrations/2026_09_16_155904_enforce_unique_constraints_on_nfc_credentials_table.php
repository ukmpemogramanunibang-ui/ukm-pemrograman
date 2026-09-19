<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // STEP 1: Tambah kolom yang belum ada
        // ============================================================

        $this->addColumnIfMissing('status', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('is_revoked');
        });

        $this->addColumnIfMissing('nfc_identifier', function (Blueprint $table) {
            $table->string('nfc_identifier', 64)->nullable()->after('nfc_uuid');
        });

        $this->addColumnIfMissing('registered_at', function (Blueprint $table) {
            $table->timestamp('registered_at')->nullable()->after('generated_at');
        });

        $this->addColumnIfMissing('revoked_at', function (Blueprint $table) {
            $table->timestamp('revoked_at')->nullable()->after('is_revoked');
        });

        $this->addColumnIfMissing('notes', function (Blueprint $table) {
            $table->text('notes')->nullable();
        });

        // ============================================================
        // STEP 2: Backfill status untuk data lama
        // ============================================================

        DB::table('nfc_credentials')->where('is_revoked', true)
            ->update(['status' => 'revoked']);

        DB::table('nfc_credentials')->where('is_revoked', false)
            ->where('is_active', true)
            ->update(['status' => 'active']);

        DB::table('nfc_credentials')->where('is_revoked', false)
            ->where('is_active', false)
            ->update(['status' => 'inactive']);

        // ============================================================
        // STEP 3: Tambah index yang belum ada
        // ============================================================

        $this->addIndexIfMissing('nfc_credentials_token_unique', function (Blueprint $table) {
            $table->unique('token', 'nfc_credentials_token_unique');
        });

        $this->addIndexIfMissing('nfc_credentials_status_idx', function (Blueprint $table) {
            $table->index(['is_active', 'is_revoked', 'status'], 'nfc_credentials_status_idx');
        });

        $this->addIndexIfMissing('nfc_credentials_identifier_unique', function (Blueprint $table) {
            $table->unique('nfc_identifier', 'nfc_credentials_identifier_unique');
        });
    }

    public function down(): void
    {
        $this->dropIndexIfExists('nfc_credentials_identifier_unique');
        $this->dropIndexIfExists('nfc_credentials_status_idx');
        $this->dropIndexIfExists('nfc_credentials_token_unique');

        foreach (['status', 'nfc_identifier', 'registered_at', 'revoked_at', 'notes'] as $column) {
            if (Schema::hasColumn('nfc_credentials', $column)) {
                Schema::table('nfc_credentials', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function addColumnIfMissing(string $column, \Closure $definition): void
    {
        if (Schema::hasColumn('nfc_credentials', $column)) {
            return;
        }

        Schema::table('nfc_credentials', function (Blueprint $table) use ($definition) {
            $definition($table);
        });
    }

    private function addIndexIfMissing(string $indexName, \Closure $definition): void
    {
        if ($this->indexExists($indexName)) {
            return;
        }

        Schema::table('nfc_credentials', function (Blueprint $table) use ($definition) {
            $definition($table);
        });
    }

    private function dropIndexIfExists(string $indexName): void
    {
        if (! $this->indexExists($indexName)) {
            return;
        }

        Schema::table('nfc_credentials', function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName, null);
        });
    }

    private function indexExists(string $indexName): bool
    {
        $result = DB::select(
            'SHOW INDEX FROM `nfc_credentials` WHERE Key_name = ?',
            [$indexName]
        );

        return ! empty($result);
    }
};