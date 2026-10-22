<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('group_id')->constrained('lms_groups')->cascadeOnDelete();
            $table->string('type');                 // task|material
            $table->string('title');
            $table->longText('description')->nullable();

            // Attachment
            $table->json('attachments')->nullable();   // max 5 image, total 100MB
            $table->string('video_url')->nullable();
            $table->string('video_provider')->nullable(); // youtube|instagram|tiktok

            // Waktu (server-side)
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->string('timezone')->default('Asia/Jakarta');

            // Target
            $table->string('target_mode')->default('role'); // role|individual|both
            $table->unsignedInteger('recipients_count')->default(0);

            // Status internal
            $table->string('status')->default('draft'); // draft|published|archived|deleted
            $table->string('visibility')->default('public'); // public|private

            // Review
            $table->boolean('allow_resubmit')->default(false);
            $table->boolean('requires_review')->default(true);

            // Soft delete + audit
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delete_reason')->nullable();
            $table->timestamps();

            $table->index(['group_id', 'type', 'status']);
            $table->index(['deadline_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_items');
    }
};