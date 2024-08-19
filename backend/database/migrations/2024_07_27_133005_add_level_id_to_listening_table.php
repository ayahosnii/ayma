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
        Schema::table('listening', function (Blueprint $table) {
            $table->unsignedBigInteger('level_id')->after('title'); // Add the level_id column
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade'); // Add the foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('listening', function (Blueprint $table) {
            $table->dropForeign(['level_id']); // Drop the foreign key first
            $table->dropColumn('level_id'); // Then drop the column
        });
    }
};
