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
        Schema::create('group_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('group_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->enum('position', ['ketua', 'pengurus', 'anggota'])->default('anggota');
    $table->enum('status', ['aktif', 'nonaktif', 'pending'])->default('pending');
    $table->timestamp('joined_at')->nullable();
    $table->timestamp('left_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
