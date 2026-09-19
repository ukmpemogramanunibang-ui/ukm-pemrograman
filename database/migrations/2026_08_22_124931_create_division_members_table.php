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
        Schema::create('division_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('division_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->enum('position', ['ketua', 'pengurus', 'anggota'])->default('anggota');
    $table->enum('status', ['aktif', 'pending', 'nonaktif'])->default('aktif');
    $table->timestamp('joined_at')->nullable();
    $table->timestamp('left_at')->nullable();
    $table->boolean('can_view')->default(true);
    $table->boolean('can_announcement')->default(false);
    $table->boolean('can_message')->default(false);
    $table->string('custom_position')->nullable()->after('position');
    $table->boolean('can_view_members')->default(true);
    $table->boolean('can_manage_members')->default(false);
    $table->boolean('can_manage_content')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('division_members');
    }
};
