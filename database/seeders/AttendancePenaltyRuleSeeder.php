<?php

namespace Database\Seeders;

use App\Models\AttendancePenaltyRule;
use Illuminate\Database\Seeder;

class AttendancePenaltyRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'code' => 'LATE_ARRIVAL_L1', 'name' => 'Terlambat (Pertama)',
                'description' => 'Terlambat datang pada sesi absensi.',
                'violation_type' => 'late_arrival', 'penalty_type' => 'point',
                'min_occurrences' => 1, 'max_occurrences' => 2,
                'period_scope' => 'month',
                'point_penalty' => -1, 'financial_amount' => 0,
                'currency' => 'IDR', 'warning_level' => 0,
                'severity' => 'low', 'is_active' => true, 'priority' => 100,
                'auto_generate' => true, 'due_days' => 0,
            ],
            [
                'code' => 'LATE_ARRIVAL_L3', 'name' => 'Terlambat (3× dalam sebulan)',
                'description' => 'Terlambat 3 kali dalam satu bulan.',
                'violation_type' => 'late_arrival', 'penalty_type' => 'combined',
                'min_occurrences' => 3, 'max_occurrences' => null,
                'period_scope' => 'month',
                'point_penalty' => -3, 'financial_amount' => 5000,
                'currency' => 'IDR', 'warning_level' => 1,
                'severity' => 'medium',
                'escalation_actions' => ['send_warning', 'notify_leader'],
                'is_active' => true, 'priority' => 90,
                'auto_generate' => true, 'due_days' => 7,
            ],
            [
                'code' => 'UNEXCUSED_L1', 'name' => 'Bolos Tanpa Keterangan',
                'description' => 'Tidak hadir tanpa izin resmi.',
                'violation_type' => 'unexcused_absence', 'penalty_type' => 'combined',
                'min_occurrences' => 1, 'max_occurrences' => 2,
                'period_scope' => 'month',
                'point_penalty' => -2, 'financial_amount' => 10000,
                'currency' => 'IDR', 'warning_level' => 1,
                'severity' => 'medium',
                'is_active' => true, 'priority' => 100,
                'auto_generate' => true, 'due_days' => 7,
            ],
            [
                'code' => 'UNEXCUSED_L3', 'name' => 'Bolos 3× (Berat)',
                'description' => 'Bolos tanpa keterangan 3 kali dalam satu semester.',
                'violation_type' => 'unexcused_absence', 'penalty_type' => 'disciplinary',
                'min_occurrences' => 3, 'max_occurrences' => null,
                'period_scope' => 'semester',
                'point_penalty' => -6, 'financial_amount' => 25000,
                'currency' => 'IDR', 'warning_level' => 2,
                'severity' => 'high',
                'requires_review' => true, 'requires_approval' => true,
                'escalation_actions' => ['recommend_suspension'],
                'is_active' => true, 'priority' => 80,
                'auto_generate' => true, 'due_days' => 7,
            ],
            [
                'code' => 'EARLY_LEAVE', 'name' => 'Pulang Awal',
                'description' => 'Meninggalkan sesi sebelum waktu selesai.',
                'violation_type' => 'early_leave', 'penalty_type' => 'point',
                'min_occurrences' => 1, 'max_occurrences' => null,
                'period_scope' => 'month',
                'point_penalty' => -1, 'financial_amount' => 0,
                'currency' => 'IDR', 'warning_level' => 0,
                'severity' => 'low',
                'is_active' => true, 'priority' => 100,
                'auto_generate' => true, 'due_days' => 0,
            ],
            [
                'code' => 'FRAUD', 'name' => 'Manipulasi Absensi',
                'description' => 'Memanipulasi kehadiran (titip absen, spoofing, dll).',
                'violation_type' => 'attendance_fraud', 'penalty_type' => 'disciplinary',
                'min_occurrences' => 1, 'max_occurrences' => null,
                'period_scope' => 'all_time',
                'point_penalty' => -10, 'financial_amount' => 100000,
                'currency' => 'IDR', 'warning_level' => 3,
                'severity' => 'critical',
                'requires_review' => true, 'requires_approval' => true,
                'escalation_actions' => ['recommend_suspension', 'notify_owner'],
                'is_active' => true, 'priority' => 200,
                'auto_generate' => true, 'due_days' => 3,
            ],
        ];

        foreach ($rules as $r) {
            AttendancePenaltyRule::updateOrCreate(['code' => $r['code']], $r);
        }
    }
}