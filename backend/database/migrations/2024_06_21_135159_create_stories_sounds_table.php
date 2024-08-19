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
        Schema::create('stories_sounds', function (Blueprint $table) {
            $table->id();
            $table->string('sound'); // Sound URL or path
            $table->unsignedBigInteger('story_id'); // Foreign key column
            $table->timestamps(); // Created at and updated at timestamps

            // Adding the foreign key constraint
            $table->foreign('story_id')->references('id')->on('stories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stories_sounds');
    }
};
