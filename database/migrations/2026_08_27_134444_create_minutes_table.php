<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minutes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('meeting_date');
            $table->time('meeting_time')->nullable();
            $table->string('location')->nullable();
            $table->string('leader_name')->nullable();
            $table->foreignId('notulis_id')->constrained('users')->onDelete('cascade');
            $table->text('participants')->nullable();
            $table->longText('agenda')->nullable();
            $table->longText('discussion')->nullable();
            $table->longText('decisions')->nullable();
            $table->longText('follow_up')->nullable();
            $table->text('summary')->nullable();
            $table->enum('status', ['draft', 'completed', 'archived'])->default('draft');
            $table->string('google_drive_file_id')->nullable();
            $table->string('google_drive_folder_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            // Index untuk filter
            $table->index(['status', 'meeting_date']);
            $table->index('created_at');
        });

        // Tabel lampiran notulen
        Schema::create('minute_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('minute_id')->constrained('minutes')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('google_drive_file_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minute_attachments');
        Schema::dropIfExists('minutes');
    }
};