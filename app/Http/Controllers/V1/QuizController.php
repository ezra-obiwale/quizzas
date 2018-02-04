<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Entities\V1\Quiz;
use Auth;

class QuizController extends SuperController
{
    protected function model()
    {
        return Quiz::class;
    }

    protected function updateModel()
    {
        // Tie quizzes to current user
        return Quiz::where('user_id', Auth::id());
    }

    protected function destroyModel()
    {
        return $this->updateModel();
    }


    protected function validationRules(array $data, $id = null)
    {
        return [
            'name' => 'required|',
            'minutes' => 'required|integer'
        ];
    }

    protected function beforeStore(array &$data)
    {
        $data['user_id'] = Auth::id();
        $this->checkName($data);
    }

    private function checkName(array &$data)
    {
        $i =1;
        while ($this->nameExists($data['name'])) {
            $data['name'] = $data['name'] . "-" . $i++;
        }
    }

    /**
     * Check if a quiz name already exists in database.
     *
     * @param  variable  $data
     * @return boolean
     */
    private function nameExists($name)
    {
        return Quiz::where('name', $name)->count() != 0;
    }

    /**
     * Quiz questions
     *
     * @param Quiz $quiz
     * @return void
     */
    public function questions($quizId)
    {
        if (!$quiz = Quiz::find($quizId)) {
            return $this->notFoundError();
        }

        $questions = $quiz->questions()->simplePaginate();
        return $this->paginatedList($questions->toArray());
    }

    public function start(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id'
        ]);

        $quiz = Quiz::find($request->quiz_id);
        $attempt = $quiz->attempts()->create([
            'user_id' => Auth::id(),
            'total_questions' => $quiz->questions()->count(),
            'started_at' => now()
        ]);

        return $this->success($attempt);
    }

    /**
     * Users that attempted the given quiz
     *
     * @param Quiz $quiz
     * @return void
     */
    public function usersWithAttempts($quizId)
    {
        if (!$quiz = Quiz::find($quizId)) {
            return $this->notFoundError();
        }

        $users = $quiz->usersWithAttempts()->simplePaginate();
        return $this->paginatedList($users->toArray());
    }

    public function result($quizId, $attemptId)
    {
        if (!$quiz = Quiz::find($quizId)) {
            return $this->notFoundError();
        }

        $attempt = $quiz->attempts()->find($attemptId);
        if (!$attempt->stopped_at) {
            $attempt->stopped_at = now();
            $attempt->save();
        }
        return $this->success($attempt);
    }
}
