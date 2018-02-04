<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Entities\V1\Quiz;
use Illuminate\Support\Facades\Auth;

class QuizController extends SuperController
{
    protected $attempated = false;

    protected function model()
    {
        // Tie quizzes to current user
        return Quiz::where('user_id', Auth::id());
    }

    protected function indexModel()
    {
        if ($this->attempated) {
            return Quiz::whereHas('attempts', function ($builder) {
                $builder->where('id', Auth::id());
            });
        }
    }

    protected function validationRules(array $data, $id = null)
    {
        $id = $id ?: 'NULL';
        return [
            'name' => 'required|unique:quizzes,name,' . $id . ',id',
            'minutes' => 'required|integer',
            'can_pause' => 'sometimes|boolean'
        ];
    }

    /**
     * Quiz questions
     *
     * @param Quiz $quiz
     * @return void
     */
    public function questions(Quiz $quiz)
    {
        $questions = $quiz->questions()->simplePaginate();
        return $this->paginatedList($questions->all());
    }

    /**
     * Users that attempted the given quiz
     *
     * @param Quiz $quiz
     * @return void
     */
    public function usersWithAttempts(Quiz $quiz)
    {
        $users = $quiz->usersWithAttempts()->simplePaginate();
        return $this->paginatedList($users->all());
    }

    public function result(Quiz $quiz, $attemptId)
    {
        $attempt = $quiz->attempts()->find($attemptId);
        if (!$attempt->stopped_at) {
            $attempt->stopped_at = now();
            $attempt->save();
        }
        return $this->success($attempt);
    }
}
