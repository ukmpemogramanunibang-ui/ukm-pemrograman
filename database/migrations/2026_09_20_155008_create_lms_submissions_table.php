<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('item_id')->constrained('lms_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->longText('answer_text')->nullable();
            $table->string('answer_link')->nullable();
            $table->text('note')->nullable();

            $table->string('status')->default('submitted');
            // submitted|pending_review|revision|completed|late

            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('feedback')->nullable();

            $table->boolean('is_late')->default(false);
            $table->boolean('is_resubmit')->default(false);

            $table->timestamps();

            $table->index(['item_id', 'user_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_submissions');
    }
};