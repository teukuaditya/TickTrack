<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = Auth::user();
            return response()->json([
                'message' => 'Profile retrieved successfully',
                'data' => new UserResource($user)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve user',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful',
                'data' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
