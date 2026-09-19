<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('room_code')->unique();
            $table->string('room_token')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            
            // Lokasi
            $table->string('building')->nullable();
            $table->string('floor')->nullable(); // Basement 1, Lantai 2, Loteng, dll.
            $table->text('address')->nullable();
            
            // Kapasitas
            $table->integer('capacity_normal')->default(0);
            $table->integer('capacity_max')->default(0);
            
            // Status & Kondisi
            $table->string('condition')->default('Baik');
            $table->string('status')->default('Tersedia');
            
            // Penanggung Jawab (text)
            $table->string('responsible_person')->nullable();
            $table->string('responsible_phone')->nullable();
            
            // Jam Operasional (JSON)
            $table->json('operational_hours')->nullable();
            
            // QR Code
            $table->boolean('has_qr')->default(false);
            $table->string('qr_code')->nullable(); // path gambar QR
            
            // Uploads (JSON)
            $table->json('images')->nullable();
            $table->json('documents')->nullable();
            
            // Audit
            $table->timestamp('last_used_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['status', 'floor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};