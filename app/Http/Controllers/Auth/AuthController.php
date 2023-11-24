<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $auth;

    public function __construct()
    {
        $this->auth = Auth::class;
    }

    /**
     * Registration
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->createUser($request->validated());

        return $this->respondWithToken($user);
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if ($this->attemptLogin($credentials)) {
            $user = $this->auth::user();
            $token = $this->generateAccessToken($user);

            return $this->respondWithToken($user, $token, 'Successfully Login!');
        }

        return $this->unauthorizedResponse();
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->auth::user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Attempt Login
     */
    protected function attemptLogin(array $credentials): bool
    {
        return $this->auth::attempt($credentials);
    }

    /**
     * Generate Access Token
     */
    protected function generateAccessToken($user): string
    {
        return $user->createToken('token')->accessToken;
    }

    /**
     * Create User
     */
    protected function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Respond With Token
     */
    protected function respondWithToken($user, $token = null, $message = 'Success')
    {
        return response()->json([
            'message' => $message,
            'access_token' => $token ?: $this->generateAccessToken($user),
            'token_type' => 'bearer'
        ], 200);
    }

    /**
     * Unauthorized Response
     */
    protected function unauthorizedResponse()
    {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
