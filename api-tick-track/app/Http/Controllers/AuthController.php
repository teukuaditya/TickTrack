<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Http\Requests\RegisterStoreRequest;

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
    public function register(RegisterStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                'message' => 'Registration successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }


}
