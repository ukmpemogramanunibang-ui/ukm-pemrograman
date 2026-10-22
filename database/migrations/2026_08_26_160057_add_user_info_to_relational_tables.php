<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel universities
        Schema::table('universities', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('is_active');
            $table->string('nim_user')->nullable()->after('name_user');
        });

        // Tabel majors
        Schema::table('majors', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('is_active');
            $table->string('nim_user')->nullable()->after('name_user');
        });

        // Tabel jabatans
        Schema::table('jabatans', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('is_active');
            $table->string('nim_user')->nullable()->after('name_user');
        });

        // Tabel jabatan_user (sudah punya user_id)
        Schema::table('jabatan_user', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('is_active');
            $table->string('nim_user')->nullable()->after('name_user');
        });

        // Tabel roles
        Schema::table('roles', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('guard_name');
            $table->string('nim_user')->nullable()->after('name_user');
        });

        // Tabel role_has_permissions
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('role_id');
            $table->string('nim_user')->nullable()->after('name_user');
        });

        // Tabel model_has_roles (sudah punya model_id)
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->string('name_user')->nullable()->after('model_id');
            $table->string('nim_user')->nullable()->after('name_user');
        });
    }

    public function down(): void
    {
        Schema::table('universities', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
        Schema::table('majors', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
        Schema::table('jabatan_user', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropColumn(['name_user', 'nim_user']);
        });
    }
};