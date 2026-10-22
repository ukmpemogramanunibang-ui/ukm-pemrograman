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
        Schema::create('user_qr_codes', function (Blueprint $table) {
            $table->id();

            // Identitas QR
            $table->uuid('qr_uuid')->unique();
            $table->string('token', 255)->unique();

            // Relasi user
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // QR image
            $table->string('qr_image')->nullable();

            // Status
            $table->boolean('is_active')
                ->default(true)
                ->index();
            $table->boolean('is_revoked')
                ->default(false)
                ->index();

            // Tanggal & waktu
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expired_at')
                ->nullable()
                ->index();
            $table->timestamp('first_used_at')->nullable();
            $table->timestamp('last_used_at')
                ->nullable()
                ->index();

            // Statistik scan
            $table->unsignedInteger('scan_count')->default(0);
            $table->string('last_scan_ip', 45)->nullable();
            $table->string('last_scan_device')->nullable();

            // Info perangkat
            $table->string('device_name', 150)->nullable();
            $table->string('device_type', 100)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->ipAddress('ip_address')->nullable();

            // Pembatalan
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Catatan tambahan
            $table->text('notes')->nullable();

            // Timestamps & soft delete
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_qr_codes');
    }
};