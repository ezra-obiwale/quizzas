<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuizResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_responses', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('quiz_id');
            $table->unsignedInteger('attempt_id');
            $table->unsignedInteger('quiz_question_id');
            $table->string('answer');
            $table->boolean('passed');

            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->on('users')
                ->references('id')->onDelete('cascade');
            $table->foreign('quiz_id')->on('quizzes')
                ->references('id')->onDelete('cascade');
            $table->foreign('attempt_id')->on('user_quiz_attempts')
                ->references('id');
            $table->foreign('quiz_question_id')->on('quiz_questions')
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
        Schema::dropIfExists('quiz_responses');
    }
}
