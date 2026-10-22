<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
             $table->string('name', 50); // 'active', 'pending', 'suspended', 'blocked', 'expired', 'deleted'
            $table->string('display_name', 100)->nullable();
            $table->string('slug', 50)->nullable(); // opsional: untuk keperluan sistem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};