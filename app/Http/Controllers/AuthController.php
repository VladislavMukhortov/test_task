<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\RefreshRequest;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Token;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Register user
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function register(AuthRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tokenResult = $user->createToken('auth');
        $accessToken = $tokenResult->accessToken;
        $refreshToken = $this->createRefreshToken($tokenResult->token);

        return $this->respondWithToken($accessToken, $refreshToken);
    }

    /**
     * Login user
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        /** @var Token $existingAccessToken */
        $existingAccessToken = $user->tokens()->where('revoked', false)->first();

        if ($existingAccessToken) {
            $refreshToken = RefreshToken::query()->where('access_token_id', $existingAccessToken->id)->first();
            $refreshToken = $refreshToken->id ?? null;
            if (!$refreshToken) {
                $refreshToken = $this->createRefreshToken($existingAccessToken);
            }
            return $this->respondWithToken($existingAccessToken->id, $refreshToken);
        }

        $accessToken = $user->createToken('auth');
        $refreshToken = $this->createRefreshToken($accessToken->token);

        return $this->respondWithToken($accessToken->accessToken, $refreshToken);
    }

    /**
     * Refresh token
     *
     * @param RefreshRequest $request
     * @return JsonResponse
     */
    public function refresh(RefreshRequest $request): JsonResponse
    {
        $token = RefreshToken::query()->where('id', $request->refresh_token)->first();

        if (!$token || $token->revoked) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }
        /** @var User $user */
        $user = $token->accessToken->user;

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $newAccessToken = $user->createToken('auth');
        $newRefreshToken = $this->createRefreshToken($newAccessToken->token);

        $token->revoke();
        $token->accessToken->revoke();

        return response()->json([
            'access_token' => $newAccessToken->accessToken,
            'refresh_token' => $newRefreshToken,
        ]);
    }

    /**
     * Create refresh token
     *
     * @param Token $accessToken
     * @return string
     */
    private function createRefreshToken(Token $accessToken): string
    {
        $refreshToken = new RefreshToken();
        $refreshToken->id = Str::random(40);
        $refreshToken->access_token_id = $accessToken->id;
        $refreshToken->revoked = false;
        $refreshToken->expires_at = now()->addMinutes(config('auth.passwords.users.expire'));

        $refreshToken->save();

        return $refreshToken->id;
    }

    /**
     * Json response
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @return JsonResponse
     */
    private function respondWithToken(string $accessToken, string $refreshToken): JsonResponse
    {
        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }
}
