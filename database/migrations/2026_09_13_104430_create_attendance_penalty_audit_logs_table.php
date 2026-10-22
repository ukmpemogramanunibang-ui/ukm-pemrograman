<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_penalty_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('penalty_id')->constrained('attendance_penalties')->cascadeOnDelete();
            $t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('action', 48);
            $t->text('description')->nullable();
            $t->json('before')->nullable();
            $t->json('after')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();

            $t->index(['penalty_id', 'created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_penalty_audit_logs');
    }
};