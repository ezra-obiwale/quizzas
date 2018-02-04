<?php

namespace App\Entities\V1;

use Laraquick\Models\WithSoftDeletes;

class QuizResponse extends WithSoftDeletes
{
    protected $fillable = ['attempt_id', 'quiz_question_id', 'answer', 'passed'];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
