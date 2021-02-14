<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function updatePassword(Request $request){
        $validatedData = $request->validate([
            'password' => 'required',
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ]);

        $user = auth()->user();

        if(!Hash::check($validatedData['password'], $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Invalid password, please try again'
            ], 404);
        }

        $user->password = bcrypt($validatedData['new_password']);

        if($user->save()){
            return [
                'success' => true,
                'message' => 'Password updated successfully'
            ];
        }

        return response()->json([
            'success' => false,
            'message' => 'Therse is an error, please try again later'
        ], 500);
     }

    public function updateProfile(Request $request){
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.auth()->id()
        ]);

        if(auth()->user()->update($validatedData)){
            return [
                'success' => true,
                'message' => 'Updated Successfully',
                'data' => auth()->user()
            ];
        }

        return response()->json([
            'success' => false,
            'message' => 'There is a problem, please try again later'
        ], 500);
    }
}
