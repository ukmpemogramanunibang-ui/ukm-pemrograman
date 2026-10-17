<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_item_user_visibility', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('dashboard_item_id')
                ->constrained('dashboard_items')
                ->cascadeOnDelete();

            $table->boolean('is_visible')->default(true);

            $table->timestamps();

            $table->unique(['user_id', 'dashboard_item_id'], 'vis_user_item_unique');
            $table->index(['user_id', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_item_user_visibility');
    }
};