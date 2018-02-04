<?php

namespace App\Http\Controllers\V1\Auth;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Entities\V1\User;
use App\Entities\V1\Role;
use Auth;
use App\Http\Controllers\Controller;
use App;

class SocialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest']);
    }

    public function redirectToProvider($provider) {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Provider.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $user = Socialite::driver($provider)->stateless()->userFromToken($request->token);
        $userCheck = User::where('email', '=', $user->email)->first();
        $email = $user->email;

        if (!$user->email) {
            $email = 'missing' . str_random(10);
        }
        if (!empty($userCheck)) {
            $socialUser = $userCheck;
        } else {
            $socialUser = User::create([
                'name' => $user->name,
                'email' => $email,
                'password' => bcrypt(str_random(16)),
                'photo' => $user->getAvatar()
            ]);
            $socialUser->confirmEmail();
        }

        $token = Auth::login($socialUser, true);
        $expires = Auth::guard()->factory()->getTTL() * 60;

        return [
            'status' => 'ok',
            'data' => [
                'token' => $token,
                'expires' => $expires
            ]
        ];
    }
}
