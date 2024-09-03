<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign key to users table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Order details
            $table->string('order_number')->unique(); // Unique order number
            $table->decimal('total_amount', 10, 2); // Total amount of the order
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending'); // Order status

            // Shipping details
            $table->string('shipping_address'); // Shipping address
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country');
            $table->string('shipping_phone');

            // Payment details
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending'); // Payment status
            $table->enum('payment_method', ['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery'])->nullable(); // Payment method
            $table->string('transaction_id')->nullable(); // Transaction ID from payment gateway

            // Timestamps
            $table->timestamp('order_date')->useCurrent(); // Date when the order was placed
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
