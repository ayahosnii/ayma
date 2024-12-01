<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique(); // Stock Keeping Unit, unique identifier
            $table->string('slug')->unique(); // URL friendly version of the name
            $table->text('description')->nullable(); // Full description of the product
            $table->text('short_description')->nullable(); // Brief description or summary
            $table->decimal('price', 10, 2); // Price of the product
            $table->decimal('discount_price', 10, 2)->nullable(); // Discounted price if applicable
            $table->unsignedInteger('stock')->default(0); // Available stock quantity
            $table->unsignedInteger('low_stock_threshold')->nullable(); // Threshold for low stock notification
            $table->boolean('is_featured')->default(false); // Whether the product is featured

            $table->foreignId('color_id')->constrained()->onDelete('cascade');
            $table->foreignId('size_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
