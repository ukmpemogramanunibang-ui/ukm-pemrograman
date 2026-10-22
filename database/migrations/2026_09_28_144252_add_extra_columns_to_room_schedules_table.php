<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('room_schedules', 'semester')) {
                $table->string('semester', 50)->nullable()->after('end_date');
            }
            if (! Schema::hasColumn('room_schedules', 'semester_start')) {
                $table->string('semester_start', 50)->nullable()->after('semester');
            }
            if (! Schema::hasColumn('room_schedules', 'semester_end')) {
                $table->string('semester_end', 50)->nullable()->after('semester_start');
            }
            if (! Schema::hasColumn('room_schedules', 'academic_year')) {
                $table->string('academic_year', 20)->nullable()->after('semester_end');
            }
            if (! Schema::hasColumn('room_schedules', 'schedule_type')) {
                $table->string('schedule_type', 50)->default('weekly')->after('academic_year');
            }

            if (! Schema::hasColumn('room_schedules', 'audience_type')) {
                $table->string('audience_type', 50)->default('all')->after('schedule_type');
            }
            if (! Schema::hasColumn('room_schedules', 'audience_ids')) {
                $table->json('audience_ids')->nullable()->after('audience_type');
            }
            if (! Schema::hasColumn('room_schedules', 'audience_count')) {
                $table->unsignedInteger('audience_count')->default(0)->after('audience_ids');
            }

            if (! Schema::hasColumn('room_schedules', 'attendance_mode')) {
                $table->string('attendance_mode', 20)->default('none')->after('audience_count');
            }
            if (! Schema::hasColumn('room_schedules', 'auto_create_attendance')) {
                $table->boolean('auto_create_attendance')->default(false)->after('attendance_mode');
            }
            if (! Schema::hasColumn('room_schedules', 'requires_review')) {
                $table->boolean('requires_review')->default(true)->after('auto_create_attendance');
            }
            if (! Schema::hasColumn('room_schedules', 'attendance_session_id')) {
                $table->unsignedBigInteger('attendance_session_id')->nullable()->after('requires_review');
            }

            if (! Schema::hasColumn('room_schedules', 'outside_operational_reason')) {
                $table->text('outside_operational_reason')->nullable()->after('attendance_session_id');
            }
            if (! Schema::hasColumn('room_schedules', 'notes')) {
                $table->text('notes')->nullable()->after('outside_operational_reason');
            }
        });

        /* ─── Tabel kegiatan per tahap ─── */
        if (! Schema::hasTable('schedule_activities')) {
            Schema::create('schedule_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('schedule_id')
                    ->constrained('room_schedules')
                    ->cascadeOnDelete();

                $table->string('day', 10);              // monday..sunday
                $table->unsignedSmallInteger('stage');  // 1,2,3,...
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
        Schema::dropIfExists('schedule_activities');

        Schema::table('room_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'semester', 'semester_start', 'semester_end', 'academic_year', 'schedule_type',
                'audience_type', 'audience_ids', 'audience_count',
                'attendance_mode', 'auto_create_attendance', 'requires_review', 'attendance_session_id',
                'outside_operational_reason', 'notes',
            ]);
        });
    }
};