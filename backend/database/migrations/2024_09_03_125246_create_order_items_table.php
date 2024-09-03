<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign keys
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Foreign key to orders table
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key to products table

            // Order item details
            $table->integer('quantity'); // Quantity of the product ordered
            $table->decimal('price', 10, 2); // Price of the product at the time of the order
            $table->decimal('total', 10, 2); // Total price for this line item (quantity * price)

            // Additional information
            $table->string('product_name'); // Store the product name as a backup, in case the product is deleted later
            $table->text('product_description')->nullable(); // Store the product description

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
        Schema::dropIfExists('order_items');
    }
}
