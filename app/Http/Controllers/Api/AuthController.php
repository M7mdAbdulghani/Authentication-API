<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request){
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = User::create($validatedData);

        $access_token = $user->createToken('access_token')->accessToken;

        $data = $user;
        $data['access_token'] = $access_token;

        return [
            'success' => true,
            'message' => 'Registered Successfully',
            'data' => $data
        ];
    }
}
