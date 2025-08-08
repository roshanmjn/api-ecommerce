<?php

namespace App\Http\Controllers;

use App\Events\AssignRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'data' => $user->only(['id', 'name', 'email']),
            'access_token' => $token,
        ]);
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => 'asdasd'
        ]);
    }

    /**
     * Refresh access token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        // Create new token
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        // Set token expiration
        $token->expires_at = now()->addMinutes(60);
        $token->save();

        return response()->json([
            'message' => 'Token refreshed successfully',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->expires_at->toDateTimeString(),
        ]);
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        // // Get the current access token ID
        // $tokenId = $request->user()->token()->id;

        // // Find and revoke the token
        // $token = Token::find($tokenId);
        // if ($token) {
        //     $token->revoked = true;
        //     $token->save();
        // }

        // return response()->json([
        //     'message' => 'Successfully logged out'
        // ]);
    }

    /**
     * Logout from all devices
     */
    public function logoutFromAllDevices(Request $request)
    {
        //     $user = $request->user();

        //     // Revoke all tokens for the user
        //     Token::where('user_id', $user->id)->update(['revoked' => true]);

        //     return response()->json([
        //         'message' => 'Successfully logged out from all devices'
        //     ]);
    }
}
