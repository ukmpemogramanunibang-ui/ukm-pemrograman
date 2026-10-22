<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anti_cheat_events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('competition_id')->constrained()->onDelete('cascade');
    $table->foreignId('match_id')->constrained('competition_matches')->onDelete('cascade');
    $table->foreignId('participant_id')->constrained('competition_participants')->onDelete('cascade');
    $table->string('type'); // speed_anomaly, score_anomaly, multiple_session, invalid_request, etc
    $table->text('details')->nullable();
    $table->integer('risk_score')->default(0);
    $table->string('severity')->default('low'); // low, medium, high, critical
    $table->boolean('is_resolved')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
