<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('room_schedules', 'images')) {
                $table->json('images')->nullable()->after('status');
            }
        });

        // Sekalian pastikan tabel kegiatan ada (kalau sebelumnya gagal)
        if (! Schema::hasTable('schedule_activities')) {
            Schema::create('schedule_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')
                    ->constrained('room_schedules')
                    ->cascadeOnDelete();
                $table->string('day', 10);
                $table->unsignedSmallInteger('stage');
                $table->string('stage_name')->nullable();
                $table->string('activity_type', 50);
                $table->time('start_time');
                $table->time('end_time');
                $table->dateTime('start_datetime')->nullable();
                $table->dateTime('end_datetime')->nullable();
                $table->string('person')->nullable();
                $table->json('generation_ids')->nullable();
                $table->json('kelompok_ids')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->index(['schedule_id', 'day', 'stage']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('room_schedules', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};