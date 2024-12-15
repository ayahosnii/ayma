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
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Shipping company name
            $table->string('code')->unique();  // Unique code for the shipping company
            $table->string('phone')->nullable();  // Phone number of the shipping company
            $table->string('email')->nullable();  // Email address of the shipping company
            $table->string('website')->nullable();  // Website URL of the shipping company
            $table->text('description')->nullable();  // Description or notes about the company
            $table->string('address_line_1')->nullable();  // Address line 1
            $table->string('address_line_2')->nullable();  // Address line 2 (optional)
            $table->string('city')->nullable();  // City of the company
            $table->string('state')->nullable();  // State or region
            $table->string('zip_code')->nullable();  // Zip code
            $table->string('country')->nullable();  // Country
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
        Schema::dropIfExists('shipping_companies');
    }
};
