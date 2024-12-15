<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryShippingCompanyTable extends Migration
{
    public function up()
    {
        Schema::create('delivery_shipping_company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shipping_company_id');
            $table->timestamps();

            // Unique constraint to enforce one-to-one relationship
            $table->unique(['user_id', 'shipping_company_id']);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shipping_company_id')->references('id')->on('shipping_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_shipping_company');
    }
};
