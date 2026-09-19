<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_check_logs', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id', 32)->unique();

            $table->string('method', 20)->index();
            $table->string('privacy_tier', 20)->default('limited');
            $table->string('result_code', 50)->index();
            $table->boolean('ok')->default(false)->index();

            $table->string('identifier_hash', 64)->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('user_agent_hash', 64)->nullable();

            $table->unsignedBigInteger('matched_user_id')->nullable()->index();
            $table->unsignedInteger('duration_ms')->nullable();

            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_check_logs');
    }
};