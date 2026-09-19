
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
        Schema::create('jabatans', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // UUID
            $table->uuid('jabatan_uuid')->unique();

            // Informasi Jabatan
            $table->string('code', 50)->unique()->nullable();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();

            // Hirarki Jabatan
            $table->unsignedTinyInteger('level')->default(5)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Pengaturan
            $table->boolean('is_management')->default(false);
            $table->boolean('is_active')->default(true)->index();

            // Hak akses dasar
            $table->boolean('can_login')->default(true);
            $table->boolean('can_vote')->default(false);
            $table->boolean('can_manage_members')->default(false);
            $table->boolean('can_manage_events')->default(false);

            // Audit
            $table->json('settings')->nullable();

            // Timestamp
            $table->timestamps();

            // Soft Delete
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};