<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\FirebaseAuthRequest;
use App\Http\Resources\UserResource;
use App\Services\FirebaseAuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AuthController extends BaseController
{
    use ApiResponse;

    public function __construct(
        private readonly FirebaseAuthService $firebaseAuthService
    ) {}

    /**
     * Login using a Firebase ID token.
     */
    public function login(FirebaseAuthRequest $request): JsonResponse
    {
        $firebaseUser = $this->firebaseAuthService->verifyIdToken($request->firebase_token);

        if (!$firebaseUser) {
            return $this->unauthorizedResponse('Invalid Firebase token. Please sign in with Google again.');
        }

        $user  = $this->firebaseAuthService->findOrCreateUser($firebaseUser);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Logout and revoke the current Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get currently authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(new UserResource($request->user()));
    }
}
