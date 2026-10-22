<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_item_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('lms_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('new'); 
            // new|viewed|in_progress|submitted|pending_review|revision|completed|late|missed|cancelled
            $table->string('assignment_source')->default('role'); // role|individual
            $table->timestamps();

            $table->unique(['item_id', 'user_id']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_item_users');
    }
};