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
        Schema::create('investor_uploaded_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("uploaded_by");
            $table->bigInteger("invoice_id");
            $table->string("transaction_id");
            $table->bigInteger("user_id");
            $table->float("amount");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_uploaded_payments');
    }
};
