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
        Schema::create('majors', function (Blueprint $table) {

            $table->id();

            // UUID publik
            $table->uuid('major_uuid')->unique();

            // Relasi ke universitas
            $table->foreignId('university_id')
                ->constrained()
                ->cascadeOnDelete();

            // Kode jurusan (IF, SI, TI, MI, dll)
            $table->string('code', 20)
                ->nullable()
                ->index();

            // Nama jurusan
            $table->string('name')
                ->index();

            // Deskripsi jurusan
            $table->text('description')->nullable();

            // Status aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};