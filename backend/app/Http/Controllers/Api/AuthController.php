<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\FirebaseAuthRequest;
use App\Http\Resources\UserResource;
use App\Services\FirebaseAuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Login with Firebase Google Sign-In token",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"firebase_token"},
 *             @OA\Property(property="firebase_token", type="string", example="eyJhbGciOiJSUzI1NiIsImtpZCI6...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Login successful"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *                 @OA\Property(property="token", type="string", example="2|abc123...")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid Firebase token",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Invalid Firebase token")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Post(
 *     path="/api/auth/logout",
 *     summary="Logout (revoke Sanctum token)",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Logged out successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Logged out successfully")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 *
 * @OA\Get(
 *     path="/api/auth/me",
 *     summary="Get authenticated user information",
 *     tags={"Authentication"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Authenticated user",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Success"),
 *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
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
