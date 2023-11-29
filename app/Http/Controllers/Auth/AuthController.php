<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'phone' => ['numeric', 'regex:/^[0-9]{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
            ]);
            $token = $this->generateAccessToken($user);
            DB::commit();
            return $this->respondWithToken($user, $token, 'User registered successfully');
        } catch (\Exception $err) {
            DB::rollBack();
            report($err);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register user',
            ], 500);
        }
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
     * Generate Access Token
     */
    protected function generateAccessToken($user): string
    {
        return $user->createToken('token')->accessToken;
    }

    /**
     * Attempt Login
     */
    protected function attemptLogin(array $credentials): bool
    {
        return $this->auth::attempt($credentials);
    }

    /**
     * Unauthorized Response
     */
    protected function unauthorizedResponse()
    {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
