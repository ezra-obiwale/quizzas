<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\V1\Auth'], function (Router $api) {
        $api->post('signup', 'SignUpController@signUp');
        $api->post('login', 'LoginController@login');
        $api->post('social/{type}', 'SocialController@handleProviderCallback');

        $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'ResetPasswordController@resetPassword');

        $api->post('logout', 'LogoutController@logout');
        $api->post('refresh', 'RefreshController@refresh');
        $api->get('user', 'UserController@me');
    });

    $api->group([
        'middleware' => 'jwt.auth',
        'namespace' => 'App\Http\Controllers\V1'
    ], function (Router $api) {
        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function () {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);

        $api->post('user/change-password', 'Auth\UserController@changePassword');
        $api->get('user/quizzes', 'Auth\UserController@quizzes');
        $api->get('user/attempted-quizzes', 'Auth\UserController@attemptedQuizzes');

        $api->resource('quizzes', 'QuizController');
        $api->post('quizzes/start', 'QuizController@start');
        $api->get('quizzes/{quiz}/questions', 'QuizController@questions');
        $api->get('quizzes/{quiz}/result/{attemptId}', 'QuizController@result');
        $api->get('quizzes/{quiz}/users', 'QuizController@usersWithAttempts');
        $api->get('quizzes/{quizId}/attempted/{attemptId}/questions-and-answers', 'Auth\UserController@attemptedQuestionsAndAnswers');

        $api->resource('quiz-questions', 'QuizQuestionController', ['except' => ['index']]);
        $api->get('quiz-questions/{quizQuestionId}/responses', 'QuizQuestionController@responses');
        $api->post('quiz-questions/{quizQuestionId}/respond', 'QuizQuestionController@respond');
    });
});
