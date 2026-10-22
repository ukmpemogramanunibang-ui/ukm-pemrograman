<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jabatan_user', function (Blueprint $table) {
            // Primary key & UUID
            $table->id();
            $table->uuid('jabatan_user_uuid')->unique();

            // Relasi utama
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('jabatan_id')
                ->constrained()
                ->cascadeOnDelete();
        $table->foreignId('organizational_period_id')->nullable()->index();

            // Status jabatan
            $table->boolean('is_primary')->default(false);
            $table->enum('status', [
                'active',
                'inactive',
                'finished',
                'revoked',
            ])->default('active')->index();
            $table->boolean('is_active')->default(true)->index();

            // Periode jabatan
            $table->date('start_date')->nullable()->index();
            $table->date('end_date')->nullable()->index();

            // Dokumen pengangkatan
            $table->string('appointment_number')->nullable();
            $table->string('appointment_document_path')->nullable();
            $table->text('end_reason')->nullable();

            // Persetujuan (approval)
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('approval_notes')->nullable();

            // Pembatalan (revoke)
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Pelacak pembuat & pengubah
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Keterangan tambahan & pengaturan
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();

            // Unique constraint: satu user tidak boleh memiliki jabatan yang sama pada periode yang sama
            $table->unique(['user_id', 'jabatan_id', 'organizational_period_id']);

            // Timestamp & soft delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_user');
    }
};