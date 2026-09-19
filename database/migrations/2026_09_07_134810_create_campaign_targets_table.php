<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_campaign_id')->constrained()->onDelete('cascade');
            $table->morphs('targetable'); // bisa user atau generation
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_targets');
    }
};