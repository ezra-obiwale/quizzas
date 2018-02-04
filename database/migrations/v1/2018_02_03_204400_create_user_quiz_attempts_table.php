<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuizAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_quiz_attempts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('quiz_id');
            $table->timestamp('started_at');
            $table->timestamp('stopped_at');
            $table->integer('total_questions')->nullable();
            $table->integer('attempted_questions')->default(0)->nullable();
            $table->integer('passed_questions')->default(0)->nullable();

            $table->timestamps();

            $table->foreign('user_id')->on('users')
                ->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('user_quiz_attempts');
    }
}
