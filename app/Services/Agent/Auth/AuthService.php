<?php

namespace App\Services\Agent\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Attempt Login
     */
    public function attemptLogin(array $credentials)
    {
        return Auth::attempt($credentials);
    }

    /**
     * Unauthorized Response
     */
    public function unauthorizedResponse()
    {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Generate Access Token
     */
    public function generateAccessToken($user): string
    {
        return $user->createToken('token')->accessToken;
    }

    /**
     * Respond With Token
     */
    public function respondWithToken($user, $token = null, $message = 'Success')
    {
        return response()->json([
            'message' => $message,
            'access_token' => $token ?: $this->generateAccessToken($user),
            'token_type' => 'bearer'
        ], 200);
    }

    /**
     * Validator
     */
    public function validator(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'phone' => ['numeric', 'regex:/^[0-9]{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

}
