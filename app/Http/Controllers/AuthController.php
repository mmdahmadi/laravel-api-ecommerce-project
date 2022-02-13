<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'cellphone' => 'required',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cellphone' => $request->cellphone,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('myApp')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ],201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',

        ]);
        if ($validator->fails()){
            return $this->errorResponse($validator->messages(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user){
            return $this->errorResponse('User Not Found',422);
        }
        if (!Hash::check($request->password,$user->password)){
            return $this->errorResponse('Password is incorrect',422);
        }
        $token = $user->createToken('myApp')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ],200);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->successResponse('logged out', 200);
    }
}

