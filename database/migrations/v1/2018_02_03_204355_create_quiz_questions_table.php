<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('quiz_id');
            $table->text('question');
            $table->string('answer');
            $table->string('option1');
            $table->string('option2')->nullable();
            $table->string('option3')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->on('users')
                ->references('id');
            $table->foreign('quiz_id')->on('quizzes')
                ->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_questions');
    }
}
