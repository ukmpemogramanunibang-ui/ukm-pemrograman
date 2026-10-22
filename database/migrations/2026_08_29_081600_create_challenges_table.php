<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('difficulty')->default('medium'); // easy, medium, hard, expert
            $table->integer('duration_minutes')->default(30);
            $table->integer('xp_reward')->default(0);
            $table->integer('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('challenges');
    }
};