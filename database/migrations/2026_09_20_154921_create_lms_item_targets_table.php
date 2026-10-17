<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lms_item_targets')) {
            Schema::create('lms_item_targets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('lms_items')->cascadeOnDelete();
                $table->string('target_type', 20);       // role | user
                $table->unsignedBigInteger('target_id');
                $table->unsignedBigInteger('level_id')->nullable();
                $table->timestamps();

                $table->index(['item_id', 'target_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_item_targets');
    }
};