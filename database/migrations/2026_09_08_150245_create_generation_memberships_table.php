<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generation_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->enum('status', [
                'active', 'completed', 'removed', 'transferred', 'resigned'
            ])->default('active');
            $table->text('reason')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: satu user hanya satu membership aktif per generasi
            $table->unique(['generation_id', 'user_id', 'status'], 'unique_active_membership')
                ->where('status', 'active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generation_memberships');
    }
};