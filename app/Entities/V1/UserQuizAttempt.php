<?php

namespace App\Entities\V1;

use Laraquick\Models\Model;

class UserQuizAttempt extends Model
{
    protected $fillable = ['user_id', 'started_at', 'stopped_at', 'total_questions', 'attempted_questions', 'passed_questions'];
    protected $dates = ['started_at', 'stopped_at'];
}
