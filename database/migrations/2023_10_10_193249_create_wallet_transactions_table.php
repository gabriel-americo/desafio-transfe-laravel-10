<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('wallet_id')->references('id')->on('wallets')->cascadeOnDelete();
            $table->uuid('payer_id');
            $table->uuid('payee_id');
            $table->decimal('amount');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
