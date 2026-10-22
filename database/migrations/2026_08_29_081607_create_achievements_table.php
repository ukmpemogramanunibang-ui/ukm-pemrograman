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
    Schema::create('achievements', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description');
        $table->string('icon')->nullable();
        $table->integer('xp_bonus')->default(0);
        $table->integer('coin_bonus')->default(0);
        $table->string('condition_type');
        $table->integer('condition_value')->default(1);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
