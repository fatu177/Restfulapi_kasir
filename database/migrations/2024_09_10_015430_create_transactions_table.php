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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')
            ->constrained('users')
            ->cascadeOnUpdate()
            ->restrictOnDelete();
            $table->foreignUuid('product_id')
            ->constrained('products')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('booking_code');
            $table->string('total_product');
            $table->string('total_price');
            $table->string('status')->default('booked');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
