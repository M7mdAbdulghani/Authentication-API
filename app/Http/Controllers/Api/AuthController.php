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

    public function login(Request $request){
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);


        if(!auth()->attempt($validatedData)){
            return response()->json([
                'success' => false,
                'message' => 'Invalid Credentials, please try again'
            ], 401);
        }

        $access_token = auth()->user()->createToken('access_token')->accessToken;

        $data = auth()->user();
        $data['access_token'] = $access_token;

        return [
            'success' => true,
            'message' => 'Login Successfully',
            'data' => $data
        ];
    }
}
