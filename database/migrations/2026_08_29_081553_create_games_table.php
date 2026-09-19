<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('banner')->nullable();
            $table->text('description')->nullable();
            $table->string('developer')->nullable();
            $table->string('version')->default('1.0.0');

            // Sumber & Tipe
            $table->enum('source_type', ['external', 'native'])->default('native');
            $table->enum('game_type', [
                'typing', 'quiz', 'coding', 'logic', 'tebak',
                'reaction', 'custom', 'puzzle', 'geometry_dash'
            ])->nullable();
            $table->string('url')->nullable(); // untuk external
            $table->enum('integration_level', ['link', 'embedded', 'api'])->default('link')->nullable();

            // Status
            $table->enum('status', ['draft', 'testing', 'published', 'maintenance', 'archived'])->default('draft');

            // Pengaturan per tipe game (JSON)
            $table->json('game_settings')->nullable();      // untuk quiz, coding, logic, dll
            $table->json('typing_settings')->nullable();    // khusus typing
            $table->json('quiz_settings')->nullable();      // khusus quiz
            $table->json('proctoring_settings')->nullable(); // proctoring

            // Soal (JSON)
            $table->json('quiz_questions')->nullable();
            $table->json('game_questions')->nullable();

            // Metadata
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
            $table->unsignedBigInteger('visits')->default(0);
            $table->boolean('show_in_leaderboard')->default(true);
            $table->boolean('support_rating')->default(false);
            $table->boolean('support_matchmaking')->default(false);
            $table->boolean('support_challenge')->default(false);

            // Statistik
            $table->unsignedInteger('total_challenges')->default(0);
            $table->unsignedInteger('total_competitions')->default(0);
            $table->unsignedInteger('total_sessions')->default(0);
            $table->integer('player_count')->default(0);
            $table->integer('total_matches')->default(0);
            $table->timestamp('last_played_at')->nullable();

            // Cover images (JSON array)
            $table->json('cover_images')->nullable();

            // Soft delete & timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};