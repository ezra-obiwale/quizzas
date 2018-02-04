<?php

namespace App\Entities\V1;

use Laraquick\Models\WithSoftDeletes;

class Quiz extends WithSoftDeletes
{
    protected $fillable = ['user_id', 'name', 'minutes'];
    protected $table = 'quizzes';
    protected $hidden = ['pivot'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }

    public function usersWithAttempts()
    {
        return $this->belongsToMany(User::class, 'user_quiz_attempts')
            ->withTimestamps()
            ->as('attempt');
    }

    public function responses()
    {
        return $this->hasManyThrough(QuizResponse::class, QuizQuestion::class, 'quiz_id');
    }

    public function attempts() {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        unset($array['pivot']);
        return $array;
    }

}
