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
        Schema::create('investor_payment_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type')->comment('momo or Card or Bank');;
            $table->string('payment_type_id')->comment('Gateway Identifier e.g Shorcode or Merchant account Id');;
            $table->integer('result_code');
            $table->string('result_description');
            $table->string('txn_code')->unique();
            $table->double('amount', 10, 2)->default(0)->unsigned();
            $table->string('txn_cross_ref')->nullable();
            $table->string('transaction_date')->nullable();
            $table->string('public_name')->nullable();
            $table->string('txn_converstion_id')->comment('usable with M-PESA')->nullable();
            $table->string('originator_conversation_id')->comment('usable with M-PESA')->nullable();
            $table->double('balance_amount', 10, 2)->default(0.0)->unsigned();
            $table->integer('payment_status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_payment_callbacks');
    }
};
