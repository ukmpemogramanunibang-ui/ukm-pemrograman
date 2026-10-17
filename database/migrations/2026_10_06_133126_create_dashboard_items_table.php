<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dashboard_items', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['card', 'shortcut'])->index();

            // Shortcut yang ditaruh di dalam card
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('dashboard_items')
                ->nullOnDelete();

            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->text('url')->nullable();
            $table->boolean('open_in_new_tab')->default(true);

            $table->enum('audience_mode', ['all', 'roles', 'users'])
                ->default('all')
                ->index();

            $table->enum('background_type', ['plain', 'color', 'image'])
                ->default('plain');
            $table->string('background_color', 9)->nullable();
            $table->string('background_image')->nullable();

            $table->enum('category', ['formal', 'non_formal'])
                ->default('formal')
                ->index();

            $table->unsignedInteger('priority')->default(0);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_active', 'priority']);
            $table->index(['parent_id', 'type']);
            $table->index(['audience_mode', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_items');
    }
};