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
        Schema::create('investor_user_products', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("product_id");
            $table->dateTime("enrollment_date");
            $table->dateTime("deadline");
            $table->enum("status", ["active", "closed"])->default("active");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investor_user_products');
    }
};
