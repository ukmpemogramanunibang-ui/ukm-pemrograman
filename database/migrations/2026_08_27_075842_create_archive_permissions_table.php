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
        Schema::create('archive_permissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('archive_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->enum('permission', ['view', 'comment', 'edit'])->default('view');
    $table->foreignId('shared_by')->constrained('users')->onDelete('cascade');
    $table->timestamps();
    $table->unique(['archive_id', 'user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_permissions');
    }
};
