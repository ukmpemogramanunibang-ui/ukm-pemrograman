<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->string('activity_type');
            $table->text('description')->nullable();
            $table->string('recurrence_type')->default('Sekali');
            $table->json('recurrence_days')->nullable();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->date('end_date')->nullable();
            $table->boolean('requires_attendance')->default(false);
            $table->string('status')->default('Aktif');
            $table->json('images')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['room_id', 'start_datetime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_schedules');
    }
};