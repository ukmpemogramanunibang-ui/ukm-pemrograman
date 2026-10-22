<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->unique();             // HANYA SATU KALI

            // Identitas dasar
            $table->string('nim', 20)->nullable()->unique()->index();
            $table->string('name');
            $table->string('username', 100)->nullable()->unique()->index();
            $table->string('email', 255)->nullable()->unique()->index();
            $table->string('phone', 20)->nullable()->index();

            // Relasi
            $table->unsignedBigInteger('university_id')->nullable()->index();
            $table->unsignedBigInteger('major_id')->nullable()->index();
            $table->unsignedBigInteger('level_id')->nullable()->index();
            $table->unsignedBigInteger('status_id')->nullable()->index();

            // Keamanan
            $table->string('password');
            $table->string('pin', 255)->nullable();
            $table->rememberToken();                         // HANYA SATU KALI

            // QR Token
            $table->string('qr_token', 64)->nullable()->unique();

            // Tipe akun (hanya satu, pilih yang sesuai)
            $table->enum('account_type', ['official', 'guest', 'event'])
                  ->default('official')
                  ->index();

            // Keanggotaan UKM
            $table->string('member_number')->nullable()->unique()->index();
            $table->unsignedTinyInteger('ukm_id')->default(1)->index();
$table->unsignedBigInteger('event_id')->nullable()->index();

            // Verifikasi
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();

            // Keamanan login & pembatasan
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_password_confirmed_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();

            // Preferensi
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('locale')->default('id');
            $table->json('preferences')->nullable();
            $table->json('settings')->nullable();

            // Profil
            $table->string('avatar_url')->nullable();
            $table->timestamp('avatar_updated_at')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->text('bio')->nullable();
            $table->string('cover_photo')->nullable();
            $table->unsignedTinyInteger('profile_completion')->default(0);
            $table->timestamp('profile_completed_at')->nullable();

            // Notifikasi
            $table->boolean('receive_email_notification')->default(true);
            $table->boolean('receive_whatsapp_notification')->default(true);

            // Status & aktivitas
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('last_seen_at')->nullable();

            // Tracking
            $table->string('created_ip', 45)->nullable();
            $table->string('updated_ip', 45)->nullable();
            $table->string('created_device')->nullable();
            $table->string('updated_device')->nullable();
            $table->json('login_devices')->nullable();

            // Riwayat
            $table->timestamp('last_failed_login_at')->nullable();
            $table->string('last_failed_login_ip', 45)->nullable();
            $table->timestamp('last_password_reset_at')->nullable();
            $table->timestamp('last_profile_update_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel password_reset_tokens (standar Laravel)
         Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};