<?php

namespace App\Entities\V1;

use Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Auth;
use Mail;
use App\Mail\ConfirmationAccount;
use Illuminate\Notifications\Notifiable;
use TCG\Voyager\Models\User as TCGUser;
use Laraquick\Models\Traits\Helper;

class User extends TCGUser implements JWTSubject
{
    use Notifiable, Helper;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'name', 'email', 'password', 'photo', 'token', 'verified', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'token'
    ];

    /**
     *
     * Boot the model.
     *
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->with('role');
        });

        static::creating(function ($user) {
            $user->token = str_random(40);
        });
    }

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Confirm the user.
     *
     * @return void
     */
    public function confirmEmail()
    {
        $this->verified = true;
        $this->token = null;

        $this->save();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role->name == 'admin';
    }

    public function sendRegistrationEmail()
    {
        Mail::to($this->email)->send(new ConfirmationAccount($this));
    }

    public function verifyPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function attemptedQuizzes()
    {
        return $this->belongsToMany(Quiz::class, 'user_quiz_attempts')
            ->withTimestamps()
            ->as('attempted_quizzes');
    }

    public function responses()
    {
        return $this->hasMany(QuizResponses::class)->with('question');
    }

}
