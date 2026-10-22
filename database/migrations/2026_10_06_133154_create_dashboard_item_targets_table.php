<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_item_targets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dashboard_item_id')
                ->constrained('dashboard_items')
                ->cascadeOnDelete();

            $table->foreignId('role_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();

            $table->timestamps();

            $table->index(['dashboard_item_id', 'role_id']);
            $table->index(['dashboard_item_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_item_targets');
    }
};