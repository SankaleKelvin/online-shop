<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:3|confirmed'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        try{
            $user->save();
            return response()->json([
                'message'=>'User registered successfully.',
                'user'=>$user
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                'message'=>'Failed to register user.',
                'error'=>$e->getMessage()
            ],500);
        }
    }

    public function login(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:3'
        ]);

        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response()->json([
                'message'=>'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message'=>'Login Successful!',
            'user'=>$user,
            'token'=>$token
        ], 201);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json("Logout Successful");
    }

}
