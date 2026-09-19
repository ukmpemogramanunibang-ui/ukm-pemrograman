<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_histories', function (Blueprint $table) {
            // Primary key & UUID
            $table->id();
            $table->uuid('point_history_uuid')->unique();

            // User terkait
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->index();

            // Pemberi poin
            $table->foreignId('given_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Periode organisasi
            $table->foreignId('organizational_period_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Jenis dan kategori poin
            $table->enum('type', ['plus', 'minus'])
                ->default('plus')
                ->index();
            $table->enum('category', [
                'attendance',
                'event',
                'competition',
                'achievement',
                'committee',
                'leadership',
                'reward',
                'punishment',
                'manual',
                'other',
            ])->default('other')->index();

            // Jumlah poin
            $table->integer('points')->index();

            // Deskripsi dan catatan
            $table->text('description')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('approval_notes')->nullable();

            // Status approval
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('approved')
                ->index();

            // Persetujuan
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('updated_approval_at')->nullable();

            // Referensi polymorphic
            $table->nullableMorphs('pointable');
            $table->string('reference_number', 100)
                ->nullable()
                ->unique()
                ->index();

            // Informasi tambahan
            $table->string('source', 100)
                ->nullable()
                ->index();
            $table->timestamp('expired_at')
                ->nullable()
                ->index();

            // Tracking pembuatan & perubahan
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->ipAddress('created_ip')->nullable();
            $table->string('created_device')->nullable();

            // Metadata
            $table->json('settings')->nullable();

            // Timestamp & soft delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};