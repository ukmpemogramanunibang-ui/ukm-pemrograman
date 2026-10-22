<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Backup data lama
        $rows = DB::table('notifications')->get();

        // 2. Drop tabel lama
        Schema::dropIfExists('notifications');

        // 3. Recreate dengan id UUID
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('read_at');
            $table->index(['user_id', 'read_at']);
        });

        // 4. Restore data dengan UUID baru
        foreach ($rows as $row) {
            DB::table('notifications')->insert([
                'id'         => (string) Str::uuid(),
                'user_id'    => $row->user_id ?? null,
                'type'       => $row->type ?? 'unknown',
                'data'       => $row->data ?? '{}',
                'read_at'    => $row->read_at ?? null,
                'created_at' => $row->created_at ?? now(),
                'updated_at' => $row->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        // tidak reversible
    }
};