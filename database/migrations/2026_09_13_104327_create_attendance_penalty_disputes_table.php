<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_penalty_disputes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('penalty_id')->constrained('attendance_penalties')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->text('reason');
            $t->string('status', 24)->default('open');
            $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->text('review_notes')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->json('attachments')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('attendance_penalty_disputes');
    }
};