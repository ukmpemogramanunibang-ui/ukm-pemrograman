<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql_protected')->create('protected_users', function (Blueprint $table) {
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
            $table->json('full_data'); // snapshot lengkap user
            $table->json('related_data')->nullable(); // points, qr, dll.
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_protected')->dropIfExists('protected_users');
    }
};