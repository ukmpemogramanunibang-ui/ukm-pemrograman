<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets_custom', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('nim', 20)->nullable();
            $table->string('token', 10); // OTP 6 digit
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets_custom');
    }
};