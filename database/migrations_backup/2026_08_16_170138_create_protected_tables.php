<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = 'mysql_protected';

        // Pastikan koneksi tersedia
        if (!config("database.connections.$connection")) {
            throw new \Exception("Koneksi database '$connection' tidak ditemukan di config/database.php");
        }

        Schema::connection($connection)->create('protected_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('nim')->nullable();
            $table->string('member_number')->nullable();
            $table->unsignedInteger('level_id')->nullable();
            $table->string('level_name')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->string('status_name')->nullable();
            $table->json('full_data');
            $table->json('related_data')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection($connection)->create('protected_points', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->bigInteger('total_points')->default(0);
            $table->json('point_details')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->index('user_uuid');
        });

        Schema::connection($connection)->create('protected_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->uuid('qr_uuid')->unique();
            $table->text('qr_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('qr_data')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->index('user_uuid');
        });

        Schema::connection($connection)->create('protected_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->integer('points');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->index('user_uuid');
            $table->index('transaction_date');
        });

        Schema::connection($connection)->create('protected_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->nullable();
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->timestamps();
            $table->index('user_uuid');
            $table->index('action');
            $table->index('logged_at');
        });

        Schema::connection($connection)->create('protected_backup_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('backup_name')->unique();
            $table->string('backup_type');
            $table->json('stats')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('backup_type');
        });
    }

    public function down(): void
    {
        $connection = 'mysql_protected';
        Schema::connection($connection)->dropIfExists('protected_backup_metadata');
        Schema::connection($connection)->dropIfExists('protected_activity_logs');
        Schema::connection($connection)->dropIfExists('protected_transactions');
        Schema::connection($connection)->dropIfExists('protected_qr_codes');
        Schema::connection($connection)->dropIfExists('protected_points');
        Schema::connection($connection)->dropIfExists('protected_users');
    }
};