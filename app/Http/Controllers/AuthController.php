<?php

namespace App\Http\Controllers;

use App\Events\AssignRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use App\Models\RefreshToken;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:40',
            // 'email' => 'required|string|email|max:60|unique:users,email',
            'email' => 'required|string|email|max:60',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        event(new AssignRole($user));

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * User login
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        try {
            if (!Auth::attempt($data)) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $user        = Auth::user();
            $accessToken = JWTAuth::fromUser($user);

            // Create refresh token (rotating)
            $rawRefresh = Str::random(64);
            $refresh = new RefreshToken();
            $refresh->user_id = $user->id;
            $refresh->token_hash = hash('sha256', $rawRefresh);
            $refresh->expires_at = now()->addDays(7);
            $refresh->created_by_ip = $request->ip();
            $refresh->user_agent = (string) $request->header('User-Agent');
            $refresh->save();

            $cookie = cookie(
                'rt',
                $rawRefresh,
                60 * 24 * 7,
                '/',
                null,
                true,
                false, //true,
                false,
                null, //'Strict'
            );

            return response()->json([
                'data' => $user->only(['id', 'name', 'email']),
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ])->cookie($cookie);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    /**
     * Get authenticated user details
     */
    public function user()
    {
        return response()->json(Auth::user());
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        try {
            // Invalidate the JWT token
            JWTAuth::invalidate(JWTAuth::getToken());

            // Get refresh token from cookie
            $refreshToken = $request->cookie('rt');

            if ($refreshToken) {
                // Delete the refresh token from database
                RefreshToken::where('token_hash', hash('sha256', $refreshToken))
                    ->where('user_id', Auth::id())
                    ->delete();
            }

            // Clear the refresh token cookie
            $cookie = cookie()->forget('rt');

            return response()->json([
                'message' => 'Successfully logged out'
            ])->cookie($cookie);
        } catch (JWTException $e) {
            logger()->error('Logout failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    /**
     * Logout from all devices
     */
    public function logoutFromAllDevices(Request $request)
    {
        try {
            // Invalidate current token
            JWTAuth::invalidate(JWTAuth::getToken());

            // Delete all refresh tokens for the user
            RefreshToken::where('user_id', Auth::id())->delete();

            // Clear the refresh token cookie
            $cookie = cookie()->forget('rt');

            return response()->json([
                'message' => 'Successfully logged out from all devices'
            ])->cookie($cookie);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }
}
