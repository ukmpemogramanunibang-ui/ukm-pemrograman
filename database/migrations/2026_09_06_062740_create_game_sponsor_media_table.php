<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('game_sponsor_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_sponsor_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'video']);
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_clickable')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_sponsor_media');
    }
};