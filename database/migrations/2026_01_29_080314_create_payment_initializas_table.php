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
        Schema::create('investor_payment_initializas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('payment_type')->default('M-PESA');
            $table->foreignId('invoice_id')->unsigned()->constrained('investor_invoices')->onDelete('cascade');
            $table->string('phone_number');
            $table->double('amount', 10, 2)->unsigned();
            $table->string('txn_ref');
            $table->enum('txn_type', ['c2b', 'b2c', 'b2b', 'reversal'])->default('c2b');
            $table->integer('_status')->default(0);
            $table->string('txn_converstion_id')->comment('usable with M-PESA')->nullable();
            $table->string('originator_conversation_id')->comment('usable with M-PESA')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists( 'investor_payment_initializas');
    }
};
