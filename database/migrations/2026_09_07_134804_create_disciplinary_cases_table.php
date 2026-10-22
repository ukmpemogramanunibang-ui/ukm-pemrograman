<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('late_case_id')->nullable()->constrained();
            $table->enum('severity', ['light', 'medium', 'heavy'])->default('light');
            $table->text('description');
            $table->enum('status', ['open', 'under_review', 'closed'])->default('open');
            $table->enum('decision', ['warning', 'suspension', 'blacklist', 'no_action'])->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->text('decision_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_cases');
    }
};