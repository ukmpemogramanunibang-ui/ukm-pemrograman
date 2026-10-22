<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_ledger', function (Blueprint $table) {
            $table->id();
            $table->morphs('source'); // invoice, payment, sponsorship, etc.
            $table->string('type'); // debit, credit
            $table->bigInteger('amount');
            $table->string('category'); // cash, sponsor, event, donation, etc.
            $table->text('description')->nullable();
            $table->date('entry_date');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_ledger');
    }
};