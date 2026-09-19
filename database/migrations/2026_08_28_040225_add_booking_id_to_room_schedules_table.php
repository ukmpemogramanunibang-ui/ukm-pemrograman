<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('room_schedules', 'booking_id')) {
                $table->foreignId('booking_id')
                    ->nullable()
                    ->constrained('bookings')
                    ->onDelete('set null')
                    ->after('room_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('room_schedules', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }
        });
    }
};