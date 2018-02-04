<?php

namespace App\Entities\V1;

use Illuminate\Database\Eloquent\Model;

class UserQuizAttempt extends Model
{
    protected $fillable = ['started_at', 'stopped_at', 'total_questions', 'attempted_questions', 'passed_questions'];
}
