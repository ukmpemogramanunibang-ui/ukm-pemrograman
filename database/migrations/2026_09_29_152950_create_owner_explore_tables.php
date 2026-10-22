<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owner_explore_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->string('color', 20)->default('indigo'); // indigo, amber, emerald, rose, etc
            $table->timestamps();

            $table->unique(['owner_id', 'name'], 'oef_owner_name_unique');
        });

        Schema::create('owner_explore_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')
                ->constrained('owner_explore_folders')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('last_explored_at')->nullable();
            $table->unsignedInteger('explore_count')->default(0);
            $table->timestamps();

            $table->unique(['folder_id', 'user_id'], 'oec_folder_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_explore_cards');
        Schema::dropIfExists('owner_explore_folders');
    }
};