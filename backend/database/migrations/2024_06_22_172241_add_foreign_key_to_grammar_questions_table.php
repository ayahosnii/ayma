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
        Schema::table('grammar_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('grammar_game_id')->after('text');

            $table->foreign('grammar_game_id')->references('id')->on('grammar_games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grammar_questions', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['grammar_game_id']);

            // Then drop the column
            $table->dropColumn('grammar_game_id');
        });
    }
};
