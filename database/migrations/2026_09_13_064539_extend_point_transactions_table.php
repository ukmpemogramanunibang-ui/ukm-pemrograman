<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // 1. attendance_point_rules
        // ============================================================
        if (!Schema::hasTable('attendance_point_rules')) {
            Schema::create('attendance_point_rules', function (Blueprint $t) {
                $t->id();
                $t->string('code', 60)->unique();
                $t->string('name');
                $t->text('description')->nullable();
                $t->integer('points');
                $t->json('conditions')->nullable();
                $t->unsignedSmallInteger('priority')->default(100);
                $t->boolean('active')->default(true);
                $t->date('effective_from')->nullable();
                $t->date('effective_until')->nullable();
                $t->unsignedSmallInteger('version')->default(1);
                $t->timestamps();
            });
        }

        // ============================================================
        // 2. point_transactions — adaptif ke skema asli
        // ============================================================
        if (!Schema::hasTable('point_transactions')) {
            throw new \RuntimeException('Tabel point_transactions tidak ditemukan.');
        }

        $has = fn (string $col) => Schema::hasColumn('point_transactions', $col);

        // Pastikan FK user benar-benar ada — kalau tidak, buat baru
        $userColumn = $has('user_id') ? 'user_id' : null;

        Schema::table('point_transactions', function (Blueprint $t) use ($has, $userColumn) {

            // --- Denormalized user_id (untuk query cepat & integritas) ---
            if (!$has('user_id')) {
                // place after point_account_id if exists, else after id
                if ($has('point_account_id')) {
                    $t->unsignedBigInteger('user_id')->nullable()->after('point_account_id');
                } else {
                    $t->unsignedBigInteger('user_id')->nullable();
                }
                $t->index('user_id', 'pt_user_idx');
            }

            // --- Signed points (untuk agregasi cepat) ---
            if (!$has('points')) {
                $after = $has('amount') ? 'amount' : ($has('type') ? 'type' : null);
                $col = $t->bigInteger('points')->nullable()->default(0);
                if ($after) $col->after($after);
                $t->index('points', 'pt_points_idx');
            }

            // --- Klasifikasi bisnis (hadir tepat waktu, bolos, dll) ---
            if (!$has('entry_type')) {
                $after = $has('reason') ? 'reason' : null;
                $col = $t->string('entry_type', 60)->nullable();
                if ($after) $col->after($after);
                $t->index('entry_type', 'pt_entry_type_idx');
            }

            // --- Traceability ke modul absensi ---
            if (!$has('attendance_session_id')) {
                $after = $has('source_id') ? 'source_id' : null;
                $col = $t->unsignedBigInteger('attendance_session_id')->nullable();
                if ($after) $col->after($after);
                $t->index('attendance_session_id', 'pt_session_idx');
            }
            if (!$has('attendance_record_id')) {
                $t->unsignedBigInteger('attendance_record_id')->nullable()->after('attendance_session_id');
                $t->index('attendance_record_id', 'pt_record_idx');
            }
            if (!$has('attendance_participant_id')) {
                $t->unsignedBigInteger('attendance_participant_id')->nullable()->after('attendance_record_id');
                $t->index('attendance_participant_id', 'pt_participant_idx');
            }
            if (!$has('point_rule_id')) {
                $t->unsignedBigInteger('point_rule_id')->nullable()->after('attendance_participant_id');
                $t->index('point_rule_id', 'pt_rule_idx');
            }

            // --- Workflow status ---
            if (!$has('status')) {
                $t->string('status', 20)->nullable()->default('final')->after('point_rule_id');
                $t->index('status', 'pt_status_idx');
            }

            // --- Reversal ---
            if (!$has('reverses_transaction_id')) {
                $t->unsignedBigInteger('reverses_transaction_id')->nullable()->after('status');
                $t->index('reverses_transaction_id', 'pt_reverses_idx');
            }
            if (!$has('corrected_by')) {
                $t->unsignedBigInteger('corrected_by')->nullable()->after('reverses_transaction_id');
                $t->index('corrected_by', 'pt_correctedby_idx');
            }
            if (!$has('finalized_at')) {
                $t->timestamp('finalized_at')->nullable()->after('corrected_by');
            }
            if (!$has('reversed_at')) {
                $t->timestamp('reversed_at')->nullable()->after('finalized_at');
            }
            if (!$has('reversal_reason')) {
                $t->text('reversal_reason')->nullable()->after('reversed_at');
            }

            // --- Metadata ---
            if (!$has('period_key')) {
                $t->string('period_key', 20)->nullable()->after('reversal_reason');
                $t->index('period_key', 'pt_period_idx');
            }
            if (!$has('idempotency_key')) {
                $t->string('idempotency_key', 64)->nullable()->after('period_key');
                $t->unique('idempotency_key', 'pt_idem_unique');
            }
            if (!$has('meta')) {
                $t->json('meta')->nullable()->after('idempotency_key');
            }
        });

        // ============================================================
        // 3. BACKFILL data lama
        // ============================================================

        // 3a. Backfill user_id dari point_accounts
        if (Schema::hasColumn('point_transactions', 'user_id')
            && Schema::hasTable('point_accounts')
            && Schema::hasColumn('point_accounts', 'user_id')) {
            DB::statement("
                UPDATE point_transactions pt
                JOIN point_accounts pa ON pt.point_account_id = pa.id
                SET pt.user_id = pa.user_id
                WHERE pt.user_id IS NULL
            ");
        }

        // 3b. Backfill points dari type+amount
        if (Schema::hasColumn('point_transactions', 'points')
            && Schema::hasColumn('point_transactions', 'type')
            && Schema::hasColumn('point_transactions', 'amount')) {
            DB::statement("
                UPDATE point_transactions
                SET points = CASE
                    WHEN type = 'debit'  THEN -1 * ABS(amount)
                    WHEN type = 'credit' THEN       ABS(amount)
                    ELSE 0
                END
                WHERE points IS NULL OR points = 0
            ");
        }

        // 3c. Backfill entry_type dari source_type
        if (Schema::hasColumn('point_transactions', 'entry_type')) {
            DB::statement("
                UPDATE point_transactions
                SET entry_type = COALESCE(source_type, 'MANUAL_ADJUSTMENT')
                WHERE entry_type IS NULL
            ");
        }

        // 3d. Backfill status
        if (Schema::hasColumn('point_transactions', 'status')) {
            DB::statement("
                UPDATE point_transactions
                SET status = 'final'
                WHERE status IS NULL
            ");
        }

        // 3e. Backfill period_key
        if (Schema::hasColumn('point_transactions', 'period_key')) {
            DB::statement("
                UPDATE point_transactions
                SET period_key = DATE_FORMAT(created_at, '%Y-%m')
                WHERE period_key IS NULL
            ");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('point_transactions')) return;

        $drop = [
            'user_id', 'points', 'entry_type',
            'attendance_session_id', 'attendance_record_id',
            'attendance_participant_id', 'point_rule_id',
            'status', 'reverses_transaction_id', 'corrected_by',
            'finalized_at', 'reversed_at', 'reversal_reason',
            'period_key', 'idempotency_key', 'meta',
        ];

        $existing = Schema::getColumnListing('point_transactions');
        $toDrop   = array_values(array_intersect($drop, $existing));

        if (!empty($toDrop)) {
            Schema::table('point_transactions', function (Blueprint $t) use ($toDrop) {
                foreach ($toDrop as $col) {
                    try { $t->dropColumn($col); } catch (\Throwable $e) {}
                }
            });
        }

        Schema::dropIfExists('attendance_point_rules');
    }
};