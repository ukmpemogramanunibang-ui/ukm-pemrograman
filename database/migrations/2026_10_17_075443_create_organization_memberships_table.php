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
    Schema::create('organization_memberships', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('organization_id')->index();
        $table->unsignedBigInteger('user_id')->index();
        $table->string('access_role', 30)->default('member');       // leader | officer | pj | member
        $table->string('membership_status', 20)->default('active'); // active | inactive | alumni
        $table->string('position_label', 100)->nullable();          // "Ketua BEM", "Sekretaris", dsb
        $table->unsignedBigInteger('jabatan_id')->nullable()->index();  // link ke jabatans existing
        $table->unsignedBigInteger('period_id')->nullable()->index();   // link ke organizational_periods existing
        $table->date('joined_at')->nullable();
        $table->date('left_at')->nullable();
        $table->json('permissions')->nullable();                    // izin spesifik per user per organisasi
        $table->timestamps();

        $table->unique(['organization_id', 'user_id'], 'org_user_unique');
        $table->index(['organization_id', 'access_role']);
        $table->index(['user_id', 'membership_status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_memberships');
    }
};
