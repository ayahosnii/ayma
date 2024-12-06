<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign Key to Users Table
            $table->unsignedInteger('loyalty_points')->default(0); // Loyalty Points
            $table->string('preferred_payment_method')->nullable(); // Preferred Payment Method
            $table->timestamp('last_purchase_date')->nullable(); // Last Purchase Date
            $table->json('customer_preferences')->nullable(); // Customer Preferences
            $table->timestamps(); // Created_At & Updated_At
            $table->softDeletes(); // Deleted_At for Soft Deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
