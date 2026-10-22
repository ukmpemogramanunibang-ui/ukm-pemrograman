<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            
            // Pemohon (Bisa Public)
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_name');
            $table->string('requester_phone');
            $table->string('requester_email')->nullable();
            $table->string('requester_nim')->nullable();
            $table->string('institution')->nullable(); // UKM, Dosen, Umum
            
            $table->string('title');
            $table->text('purpose')->nullable();
            $table->integer('participant_count')->default(0);
            
            // Waktu
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            
            // Status (Draft, Menunggu, Disetujui, Ditolak, Selesai, Kadaluarsa, Dibatalkan)
            $table->string('status')->default('Menunggu Persetujuan');
            $table->string('approval_status')->default('Pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            // Kategori & Prioritas
            $table->string('activity_type')->default('Rapat');
            $table->string('priority')->default('Biasa');
            
            // Token untuk pengecekan publik
            $table->string('public_token')->nullable()->unique();
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['room_id', 'start_datetime', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};