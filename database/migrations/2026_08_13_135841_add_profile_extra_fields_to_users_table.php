<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('country');
            }
            if (!Schema::hasColumn('users', 'semester')) {
                $table->string('semester')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'portfolio_url')) {
                $table->string('portfolio_url')->nullable()->after('semester');
            }
            if (!Schema::hasColumn('users', 'social_media')) {
                $table->json('social_media')->nullable()->after('portfolio_url');
            }
            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable()->after('social_media');
            }
            if (!Schema::hasColumn('users', 'pin')) {
                $table->string('pin', 10)->nullable()->after('skills');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'semester', 'portfolio_url', 'social_media', 'skills', 'pin']);
        });
    }
};