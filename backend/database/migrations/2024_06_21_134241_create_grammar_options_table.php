<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrammarOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('grammar_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grammar_question_id');
            $table->text('option');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('grammar_question_id')->references('id')->on('grammar_questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('grammar_options');
    }
}
