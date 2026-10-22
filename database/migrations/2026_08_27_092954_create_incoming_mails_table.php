<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_mails', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->string('pengirim');
            $table->string('perihal');
            $table->string('kategori')->default('Umum'); // Undangan, Kerjasama, Pemberitahuan, Lainnya
            $table->string('tujuan')->nullable(); // Ditujukan kepada
            $table->foreignId('dicatat_oleh')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['baru', 'dibaca', 'diproses', 'diteruskan', 'selesai', 'diarsipkan'])->default('baru');
            $table->string('lampiran')->nullable(); // Path file
            $table->text('catatan')->nullable();
            $table->foreignId('diteruskan_kepada')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diteruskan_pada')->nullable();
            $table->timestamp('diarsipkan_pada')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Index untuk filter
            $table->index(['status', 'kategori']);
            $table->index('tanggal_diterima');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_mails');
    }
};