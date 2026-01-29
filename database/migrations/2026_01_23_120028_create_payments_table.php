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
        Schema::create('investor_payments', function (Blueprint $table) {
            $table->id();
            $table->integer("transaction_amount");
            $table->integer("transaction_reference");
            $table->string("phone_number");
            $table->integer("user_id");
            $table->dateTime("transaction_date");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_payments');
    }
};
