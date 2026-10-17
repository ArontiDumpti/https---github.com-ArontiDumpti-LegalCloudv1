<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Audit\AuditLogger;
use App\Domain\Identity\ApiTokenService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request, ApiTokenService $tokens, AuditLogger $audit): JsonResponse
    {
        $user = User::query()->where('email_hash', User::emailLookupHash($request->validated('email')))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password) || $user->status !== 'active' || ! $user->organization) {
            return response()->json(['code' => 'unauthenticated', 'message' => 'The supplied credentials are invalid.'], 401);
        }

        $token = $tokens->issue($user);
        $audit->record($request, $user, 'auth.login', 'user', $user->public_id);

        return response()->json([
            'accessToken' => $token['plainTextToken'],
            'tokenType' => 'Bearer',
            'expiresAt' => $token['expiresAt']->toISOString(),
            'user' => (new UserResource($user))->resolve(),
            'organization' => [
                'id' => $user->organization->id,
                'name' => $user->organization->name,
            ],
        ]);
    }

    public function logout(Request $request, ApiTokenService $tokens, AuditLogger $audit): JsonResponse
    {
        $user = $request->user();
        $accessToken = $request->attributes->get('api_access_token');

        if ($accessToken) {
            $tokens->revoke($accessToken);
        }

        $audit->record($request, $user, 'auth.logout', 'user', $user->public_id);

        return response()->json(null, 204);
    }
}
