<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversation_participants', function (Blueprint $table) {
            // Menambahkan kolom left_at untuk mencatat kapan user keluar dari chat
            $table->timestamp('left_at')->nullable()->after('joined_at');
            
            // Menambahkan kolom is_muted untuk fitur mute chat (default false / tidak dimute)
            $table->boolean('is_muted')->default(false)->after('left_at');
        });
    }

    public function down(): void
    {
        Schema::table('conversation_participants', function (Blueprint $table) {
            $table->dropColumn(['left_at', 'is_muted']);
        });
    }
};