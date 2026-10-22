<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_qr_recovery_snapshots', function (Blueprint $table) {
            $table->id();
            $table->uuid('recovery_uuid')->unique();
            $table->uuid('user_uuid')->unique(); // tidak ada FK
            $table->unsignedBigInteger('original_user_id')->nullable();

            $table->string('member_number')->nullable();
            $table->string('nim')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedTinyInteger('ukm_id')->default(1);

            $table->string('qr_uuid')->nullable();
            $table->string('qr_token')->nullable();
            $table->string('avatar_url')->nullable();

            $table->json('profile_snapshot')->nullable();
            $table->longText('credential_snapshot')->nullable(); // akan dienkripsi otomatis oleh cast

            $table->boolean('is_recoverable')->default(true);
            $table->timestamp('last_snapshot_at')->nullable();

            $table->timestamps();

            $table->index('original_user_id');
            $table->index('member_number');
            $table->index('nim');
            $table->index('user_uuid');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_qr_recovery_snapshots');
    }
};