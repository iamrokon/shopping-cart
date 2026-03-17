<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseAuthService
{
    private string $projectId;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
    }

    /**
     * Verify a Firebase ID token by calling the Firebase REST API.
     *
     * Returns decoded token payload on success, null on failure.
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            $response = Http::get("https://www.googleapis.com/identitytoolkit/v3/relyingparty/publicKeys");

            // Use Firebase tokeninfo endpoint to verify
            $verifyUrl = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . config('services.firebase.api_key');

            $tokenResponse = Http::post($verifyUrl, [
                'idToken' => $idToken,
            ]);

            if ($tokenResponse->failed()) {
                Log::warning('Firebase token verification failed', ['response' => $tokenResponse->json()]);
                return null;
            }

            $data = $tokenResponse->json();

            if (empty($data['users'][0])) {
                return null;
            }

            return $data['users'][0];
        } catch (\Exception $e) {
            Log::error('Firebase verification exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Find or create a user from Firebase token data.
     */
    public function findOrCreateUser(array $firebaseUser): User
    {
        return User::updateOrCreate(
            ['firebase_uid' => $firebaseUser['localId']],
            [
                'name'   => $firebaseUser['displayName'] ?? 'User',
                'email'  => $firebaseUser['email'] ?? '',
                'avatar' => $firebaseUser['photoUrl'] ?? null,
            ]
        );
    }
}
