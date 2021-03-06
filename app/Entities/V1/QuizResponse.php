<?php

namespace App\Entities\V1;

use Laraquick\Models\WithSoftDeletes;

class QuizResponse extends WithSoftDeletes
{
    protected $fillable = ['user_id', 'quiz_id', 'attempt_id', 'quiz_question_id', 'answer', 'passed'];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
