<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nfc_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->uuid('nfc_uuid')->unique();
            $table->string('token', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_revoked')->default(false);
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedInteger('scan_count')->default(0);
            $table->string('nfc_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfc_credentials');
    }
};