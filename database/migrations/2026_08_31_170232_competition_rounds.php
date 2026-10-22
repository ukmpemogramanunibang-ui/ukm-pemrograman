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
       Schema::create('competition_rounds', function (Blueprint $table) {
    $table->id();
    $table->foreignId('competition_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->integer('round_number');
    $table->string('type')->default('elimination'); // elimination, group, final
    $table->string('status')->default('upcoming'); // upcoming, active, completed
    $table->timestamp('starts_at')->nullable();
    $table->timestamp('ends_at')->nullable();
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
