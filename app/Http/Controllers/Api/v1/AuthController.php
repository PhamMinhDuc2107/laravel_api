<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;


class AuthController
{
    public function register(RegisterRequest $request):JsonResponse
    {
        $data = $request->validated();
        $user = User::create($data);
        $user->sendEmailVerificationNotification();
        Log::info('Verification email sent to ' . $user->email);
        $token = $user->createToken($request->name)->plainTextToken;
        return response()->json([
            "message" => "User created successfully",
            "user" => $user,
            "token" => $token,
        ],201);
    }
    public function login(LoginRequest $request):JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            return response()->json([
                "message" => "Invalid credentials",
            ],401);
        }
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email address is not verified.'
            ], 403);
        }
        $token = $user->createToken($user->name)->plainTextToken;
        return response()->json([
            "message" => "User logged in successfully",
            "user" => $user,
            "token" => $token,
        ],200);
    }
    public function logout(Request $request):JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json([
            "message" => "User logged out successfully",
        ],200);
    }
}
