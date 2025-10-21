<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return $this->successResponse([
                'token' => $token,
                'token_type' => 'Bearer',
                'user'  => $user,
            ], 'Registration successful.');
        } catch (\Throwable $t) {
            DB::rollBack();

            return $this->errorResponse('Registration failed.', [
                'error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }
        
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Invalid email or password.', [], 401);
            }

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'token' => $token,
                'token_type' => 'Bearer',
                'user'  => $user,
            ], 'Login successful.');
        } catch (\Throwable $t) {
            return $this->errorResponse('Login failed.', [
                'error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            // Delete the current access token
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse([], 'Logout successful.');
        } catch (\Throwable $t) {
            return $this->errorResponse('Logout failed.', [
                'error' => config('app.debug') ? $t->getMessage() : 'Something went wrong.'
            ], 500);
        }
    }
}
