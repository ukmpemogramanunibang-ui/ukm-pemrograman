<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'booking_code')) {
                $table->string('booking_code')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('bookings', 'requester_type')) {
                $table->string('requester_type', 30)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'requester_id')) {
                $table->string('requester_id', 50)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'requester_name')) {
                $table->string('requester_name')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'requester_phone')) {
                $table->string('requester_phone', 20)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'participants')) {
                $table->integer('participants')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'source')) {
                $table->string('source', 20)->default('public');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_code','requester_type','requester_id',
                'requester_name','requester_phone','participants','source'
            ]);
        });
    }
};