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
        Schema::create('investor_in_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('invoice_id');
            $table->double('payment_amount');
            $table->string('payment_source');
            $table->string('payment_source_address');
            $table->string('payment_source_txn_id');
            $table->enum('txn_status', ['approved', 'refunded'])->default('approved');
            $table->string('txn_ref')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_in_payments');
    }
};
