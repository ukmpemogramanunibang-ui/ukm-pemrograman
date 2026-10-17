<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('organizations', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->string('name', 150);
        $table->string('slug', 150)->unique();
        $table->string('short_name', 50)->nullable();
        $table->string('type', 30)->default('ukm')->index();       // bem | hima | ukm | komunitas
        $table->string('scope_type', 30)->default('minat');        // universitas | prodi | minat
        $table->unsignedBigInteger('parent_id')->nullable()->index(); // utk HIMA di bawah fakultas, dsb
        $table->unsignedBigInteger('university_id')->nullable()->index();
        $table->unsignedBigInteger('major_id')->nullable()->index();
        $table->string('status', 20)->default('active')->index();
        $table->string('logo_url')->nullable();
        $table->text('description')->nullable();
        $table->json('settings')->nullable();                       // branding, modul aktif
        $table->unsignedBigInteger('owner_id')->nullable()->index(); // siapa yang bisa manage
        $table->timestamps();
        $table->softDeletes();

        $table->index(['type', 'status']);
        $table->index(['scope_type', 'status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
