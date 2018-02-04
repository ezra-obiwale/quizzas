<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Entities\V1\QuizQuestion;
use App\Entities\V1\UserQuizAttempt;
use Illuminate\Support\Facades\DB;
use Auth;

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

    protected function beforeStore(array &$data)
    {
        $data['user_id'] = Auth::id();
    }

    public function responses(QuizQuestion $quizQuestion)
    {
        $responses = $quizQuestion->responses()
            ->where('user_id', Auth::id())
            ->simplePaginate();
        return $this->paginatedList($responses->toArray());
    }

    public function respond(Request $request, $quizQuestionId)
    {
        $request->validate([
            'attempt_id' => 'required|exists:user_quiz_attempts,id',
            'answer' => 'required'
        ]);

        $quizQuestion = QuizQuestion::with([
            'quiz.attempts' => function($builder) use($request) {
                return $builder->where('id', $request->attempt_id);
            }
        ])->find($quizQuestionId);
        
        if (!$quizQuestion) {
            return $this->notFoundError();
        } else {
            $quiz = $quizQuestion->quiz;
            // check quiz time has expired
            if (strtotime(strval($quiz->attempts->first()->started_at)) + ($quiz->minutes * 60) < time()) {
                return $this->error('Quiz time has expired', 401);
            }
        }

        $attempt = UserQuizAttempt::find($request->attempt_id);
        $response = $quizQuestion->responses()
            ->where('attempt_id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        DB::beginTransaction();
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
            $quizQuestion->responses()->create([
                'user_id' => Auth::id(),
                'quiz_id' => $quizQuestion->quiz_id,
                'attempt_id' => $request->attempt_id,
                'answer' => $request->answer,
                'passed' => $passed
            ]);
            // increment attempted questions
            $attempt->attempted_questions++;
        }

        $attempt->save();
        DB::commit();

        return $this->success([]);
    }
}
