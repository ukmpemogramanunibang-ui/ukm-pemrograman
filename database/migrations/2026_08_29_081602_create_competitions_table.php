<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('competitions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('game_id')->constrained();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('type')->default('knockout'); // knockout, round_robin, swiss
    $table->string('participant_type')->default('individual'); // individual, team, group
    $table->string('status')->default('draft'); // draft, registration, active, live, finished, cancelled
    $table->integer('max_participants')->default(32);
    $table->integer('winner_count')->default(3);
    $table->boolean('is_live')->default(false);
    $table->boolean('anti_cheat_enabled')->default(true);
    $table->boolean('auto_matchmaking')->default(true);
    $table->boolean('allow_admin_override')->default(false);
    $table->timestamp('starts_at')->nullable();
    $table->timestamp('ends_at')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    $table->softDeletes();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
