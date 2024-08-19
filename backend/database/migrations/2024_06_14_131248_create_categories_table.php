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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Category name
            $table->string('slug')->unique(); // Unique slug for SEO-friendly URLs
            $table->string('description')->nullable(); // Optional description of the category
            $table->string('image')->nullable(); // Optional image for the category
            $table->unsignedBigInteger('parent_id')->nullable(); // For nested categories, if applicable
            $table->integer('order')->default(0); // Ordering for display purposes
            $table->boolean('is_active')->default(true); // Whether the category is active or not
            $table->timestamps();

            // Foreign key constraint for parent_id
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
