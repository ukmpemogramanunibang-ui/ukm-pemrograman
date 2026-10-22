<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_penalties', function (Blueprint $t) {
            $t->id();
            $t->string('case_number', 32)->unique();

            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $t->foreignId('attendance_session_id')->nullable()->constrained('attendance_sessions')->nullOnDelete();
            $t->foreignId('attendance_record_id')->nullable()->constrained('attendance_records')->nullOnDelete();
            $t->foreignId('rule_id')->nullable()->constrained('penalty_rules')->nullOnDelete();

            $t->string('penalty_code', 64)->nullable();
            $t->string('penalty_type', 32);
            $t->string('violation_type', 48);
            $t->string('severity', 16)->default('medium');

            $t->integer('points')->default(0);
            $t->decimal('amount', 12, 2)->default(0);
            $t->decimal('amount_paid', 12, 2)->default(0);
            $t->string('currency', 8)->default('IDR');

            $t->unsignedTinyInteger('warning_level')->default(0);
            $t->string('status', 24)->default('pending');
            $t->string('payment_status', 24)->default('unpaid');

            $t->text('reason')->nullable();
            $t->text('notes')->nullable();

            $t->timestamp('issued_at')->nullable();
            $t->timestamp('due_at')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->timestamp('resolved_at')->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('approved_at')->nullable();

            $t->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('cancelled_at')->nullable();
            $t->text('cancellation_reason')->nullable();

            $t->json('meta')->nullable();

            $t->timestamps();
            $t->softDeletes();

            $t->index(['user_id', 'status']);
            $t->index(['status', 'due_at']);
            $t->index(['violation_type', 'severity']);
            $t->index(['group_id', 'status']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_penalties');
    }
};