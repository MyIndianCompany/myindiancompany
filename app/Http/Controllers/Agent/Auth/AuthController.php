<?php

namespace App\Http\Controllers\Agent\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\LoginRequest;
use App\Models\Agent\Agent;
use App\Models\Contact;
use App\Models\User;
use App\Services\Agent\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Common\Constants\Constants;

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
    public function register(Request $request, AuthService $authService)
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
                'password' => bcrypt($request->input('password')),
                'role' => Constants::USER_AGENT
            ]);
            $token = $authService->generateAccessToken($user);
            $agent = Agent::create([
                'user_id' => $user->id,
                'name' => $request->input('name'),
            ]);
            $contact = Contact::create([
                'phone' => $request->input('phone'),
                'email' => $request->input('email')
            ]);
            $agent->contacts()->attach($contact->id);
            DB::commit();
            return $authService->respondWithToken($user, $token, 'User registered successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            return response()->json([
                'message' => 'Oops! Something went wrong. Please try again later.',
                'error' => $exception->getMessage()
            ], 401);
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request, AuthService $authService)
    {
        $credentials = $request->only(['email', 'password']);

        if ($authService->attemptLogin($credentials)) {
            $user = $this->auth::user();
            $token = $authService->generateAccessToken($user);

            return $authService->respondWithToken($user, $token, 'Successfully Login!');
        }

        return $authService->unauthorizedResponse();
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->auth::user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
