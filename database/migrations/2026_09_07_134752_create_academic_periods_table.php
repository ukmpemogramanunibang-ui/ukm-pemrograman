<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_periods', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g. "Semester Ganjil 2024/2025"
    $table->enum('semester', ['Ganjil', 'Genap', 'Pendek']);
    $table->string('year', 9); // e.g. "2024/2025"
    $table->date('start_date');
    $table->date('end_date');
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(false);
    $table->softDeletes();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};