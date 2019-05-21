<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate request
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|unique:users,email|email',
            'password' => 'required'
        ]);

        // create user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //check for token

        if(!$token = auth()->attempt($request->only(['email', 'password'])))
        {
            return abort(401);
        }

        return (new UserResource($user))
                ->additional([
                    'token' => $token
                ]);
    }

    public function login(Request $request)
    {
        //validate request
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required'
        ]);
        //check for token

        if(!$token = auth()->attempt($request->only(['email', 'password'])))
        {
             return response()->json([
                'errors' => 'Invalid user name or password'
                ], 422);
        }

        return (new UserResource($request->user()))
                ->additional([
                    'token' => $token
                ]);
    }
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
