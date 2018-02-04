<?php

namespace App\Http\Controllers\V1\Auth;

use Config;
use App\Entities\V1\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Entities\V1\Role;
use Illuminate\Support\Facades\Auth;

class SignUpController extends Controller
{
    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
        $data = $request->all();
        $data['role_id'] = Role::where('name', User::count() ? 'user' : 'admin')->first()->id;
        
        $user = new User($data);
        if(!$user->save()) {
            throw new HttpException(500);
        }

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'status' => 'ok',
            'token' => $token,
            'expires_in' => Auth::guard()->factory()->getTTL() * 60
        ], 201);
    }
}
