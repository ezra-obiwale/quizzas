<?php

namespace App\Http\Controllers\V1\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Requests\V1\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;
use App\Entities\V1\User;
use App\Http\Controllers\V1\SuperController;

class UserController extends SuperController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    protected function model() {
        return User::class;
    }

    protected function validationRules(array $data, $id = null) {
        return [
            'name' => 'required',
            'email' => 'required|email',
            'photo' => 'file',
            'username' => 'unique',
        ];
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::guard()->user());
    }

    public function changePassword(Request $request) {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        if (!Auth::user()->verifyPassword($request->old_password)) {
            return $this->validationError([
                'old_password' => [
                    'Incorrect old password'
                ]
            ]);
        }

        Auth::user()->update([
            'password' => $request->new_password
        ]);

        return $this->success();
    }

    /**
     * Quizzes created by current user
     *
     * @return void
     */
    public function quizzes()
    {
        $quizzes = Auth::user()->quizzes()->simplePaginate();
        return $this->paginatedList($quizzes->all());
    }

    /**
     * Current user's attempted quizzes
     *
     * @return void
     */
    public function attemptedQuizzes()
    {
        $quizes = Auth::user()->attemptedQuizzes()->simplePaginate();
        return $this->paginatedList($quizzes->all());
    }

    public function attemptedQuestionsAndAnswers($quizId, $attemptId)
    {
        $QAs = Auth::user()->responses()
            ->where('quiz_id', $quizId)
            ->where('attempt_id', $attemptId)
            ->simplePaginate();

        return $this->paginatedList($QAs->all());
    }
}
