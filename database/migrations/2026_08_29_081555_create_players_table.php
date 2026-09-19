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
    Schema::create('players', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('nim')->unique()->nullable();
        $table->string('email')->unique();
        $table->string('password');
        $table->enum('rank', ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Master', 'Grandmaster'])->default('Bronze');
        $table->integer('rating')->default(1000);
        $table->integer('xp')->default(0);
        $table->integer('win_rate')->default(0);
        $table->integer('total_matches')->default(0);
        $table->integer('wins')->default(0);
        $table->integer('losses')->default(0);
        $table->integer('draws')->default(0);
        $table->integer('streak')->default(0);
        $table->enum('status', ['active', 'suspended', 'banned'])->default('active');
        $table->timestamp('last_active_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
