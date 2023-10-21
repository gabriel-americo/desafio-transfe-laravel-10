<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payer_wallet_id')->references('id')->on('wallets');
            $table->foreignUuid('payee_wallet_id')->references('id')->on('wallets');
            $table->decimal('amount');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
