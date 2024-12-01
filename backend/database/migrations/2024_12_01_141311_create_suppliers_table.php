<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Supplier's name
            $table->string('contact_person')->nullable(); // Contact person at the supplier
            $table->string('contact_info')->nullable(); // Contact details (phone/email)
            $table->string('phone')->nullable(); // Supplier's phone number
            $table->string('email')->nullable(); // Supplier's email
            $table->string('address')->nullable(); // Supplier's address
            $table->string('country')->nullable(); // Supplier's country
            $table->string('city')->nullable(); // Supplier's city
            $table->string('postal_code')->nullable(); // Supplier's postal/ZIP code
            $table->string('website')->nullable(); // Supplier's website
            $table->string('tax_id')->nullable(); // Tax identification number
            $table->string('status')->default('active'); // Status (e.g., active, inactive)
            $table->text('notes')->nullable(); // Optional notes
            $table->enum('supplier_type', ['local', 'international', 'wholesale', 'retail', 'factory'])->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};
