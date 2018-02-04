<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Entities\V1\QuizQuestion;
use App\Entities\V1\UserQuizAttempt;
use Illuminate\Support\Facades\DB;

class QuizQuestionController extends SuperController
{
    protected function model()
    {
        // Tie to current user
        return QuizQuestion::where('user_id', Auth::id());
    }

    protected function validationRules(array $data, $id = null)
    {
        return [
            'quiz_id' => 'required|exists:quizzes,id',
            'question' => 'required',
            'answer' => 'required',
            'option1' => 'required'
        ];
    }

    public function responses(QuizQuestion $quizQuestion)
    {
        $responses = $quizQuestion->responses()
            ->where('user_id', Auth::id())
            ->simplePaginate();
        return $this->paginatedList($responses->all());
    }

    public function respond(Request $request, $quizQuestionId)
    {
        $quizQuestion = QuizQuestion::with('quiz')->find($quizQuestionId);
        if (!$quizQuestion) {
            return $this->notFoundError();
        } else {
            $quiz = $quizQuestion->quiz;
            // check quiz time has expired
            if (strtotime(strval($quiz->started_at)) + ($quiz->minutes * 60) < time()) {
                return $this->error('Quiz time has expired', 401);
            }
        }

        $request->validate([
            'attempt_id' => 'required|exists:user_quiz_attempts,id',
            'answer' => 'required'
        ]);

        $attempt = UserQuizAttempt::find($request->attempt_id);
        $response = $quizQuestion->response()
            ->where('attempt_id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        DB::beginCommit();
        // has old response
        if ($response) {
            $passed = false;
            // got the answer corrent initially
            if ($response->answer == $quizQuestion) {
                // remove from passed questions
                $attempt->passed_questions--;
            }
            $response->answer = $request->answer;
            if ($response->answer == $quizQuestion) {
                // increased passed questions
                $attempt->passed_questions++;
                $passed = true;
            }
            $response->passed = $passed;
            $response->save();
        }
        // new response
        else {
            $passed = false;
            // is correct answer
            if ($quizQuestion->answer == $request->answer) {
                // increment passed questions
                $attempt->passed_questions++;
                $passed = true;
            }
            // create response
            $quizQuestion->response()->create([
                'user_id' => Auth::id(),
                'quiz_id' => $quizQuestion->quiz_id,
                'attempt_id' => $request->attempt_id,
                'answer' => $request->answer,
                'passed' => $pased
            ]);
            // increment attempted questions
            $attempt->attempted_questions++;
        }

        $attempt->save();
        DB::commit();

        return $this->success();
    }
}
