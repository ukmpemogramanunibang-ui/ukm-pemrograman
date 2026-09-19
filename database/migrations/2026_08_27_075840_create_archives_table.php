<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('file_name');
    $table->string('file_id'); // ID file di Google Drive
    $table->string('mime_type')->nullable();
    $table->string('google_drive_folder_id')->nullable(); // folder di Drive
    $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
    $table->enum('visibility', ['private', 'internal', 'public'])->default('internal');
    $table->timestamp('expires_at')->nullable();
    $table->softDeletes();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
