<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('decision_poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('decision_polls')->cascadeOnDelete();
            $table->string('label');
            $table->unsignedInteger('votes')->default(0);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('decision_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('decision_polls')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('decision_poll_options')->cascadeOnDelete();
            $table->string('voter_fingerprint', 64);
            $table->ipAddress('ip')->nullable();
            $table->timestamps();

            $table->unique(['poll_id', 'option_id', 'voter_fingerprint'], 'unique_vote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_poll_votes');
        Schema::dropIfExists('decision_poll_options');
    }
};