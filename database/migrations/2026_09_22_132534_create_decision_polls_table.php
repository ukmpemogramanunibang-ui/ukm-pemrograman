<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('decision_polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_token', 16)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('allow_multiple')->default(false);
            $table->unsignedTinyInteger('max_choices')->default(1);
            $table->boolean('is_anonymous')->default(true);
            $table->timestamp('closes_at')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'archived'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_polls');
    }
};