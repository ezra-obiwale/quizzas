<?php

namespace App\Entities\V1;

use Laraquick\Models\WithSoftDeletes;

class QuizQuestion extends WithSoftDeletes
{
    protected $fillable = ['quiz_id', 'user_id', 'question', 'answer', 'option1', 'option2', 'option3'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function responses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
