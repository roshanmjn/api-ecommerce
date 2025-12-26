<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RefreshTokenController extends Controller
{
    private const COOKIE_NAME = 'rt';

    public function refresh(Request $request)
    {
        $rawToken = $request->cookie(self::COOKIE_NAME) ?? $request->string('refresh_token');
        if (empty($rawToken)) {
            return response()->json(['message' => 'Missing refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $hashed = hash('sha256', (string) $rawToken);
        $stored = RefreshToken::query()
            ->where('token_hash', $hashed)
            ->first();

        if (!$stored || $stored->revoked || $stored->isExpired()) {
            return response()->json(['message' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($stored->user_id);
        if (!$user) {
            return response()->json(['message' => 'Invalid user'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $accessToken = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not create access token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Rotate refresh token
        $newRaw = Str::random(64);
        $stored->revoked = true;
        $stored->replaced_by_token_id = null; // set after save
        $stored->last_used_ip = $request->ip();
        $stored->save();

        $new = new RefreshToken();
        $new->user_id = $user->id;
        $new->token_hash = hash('sha256', $newRaw);
        $new->expires_at = now()->addDays(30);
        $new->revoked = false;
        $new->created_by_ip = $request->ip();
        $new->user_agent = (string) $request->header('User-Agent');
        $new->save();

        $stored->replaced_by_token_id = $new->id;
        $stored->save();

        $cookie = cookie(
            self::COOKIE_NAME,
            $newRaw,
            60 * 24 * 30,
            '/',
            null,
            true,
            true,
            false,
            'Strict'
        );

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ])->cookie($cookie);
    }
}


