<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'headline' => 'API member',
            'location' => 'Cambodia',
            'bio' => 'Joined from the mobile client.',
            'skills' => ['API'],
            'availability' => 'Open to collaboration',
            'portfolio_headline' => 'Building with the Khmer developer community',
            'portfolio_summary' => 'A new builder profile ready to grow into a public portfolio.',
            'social_links' => [
                'github' => null,
                'linkedin' => null,
                'portfolio' => null,
                'x' => null,
            ],
            'featured_work' => [],
            'profile_palette' => [
                'primary' => '#f97316',
                'secondary' => '#38bdf8',
                'surface' => '#0f172a',
            ],
        ]);

        $token = $user->createToken('mobile-client', ['feed:read', 'projects:read', 'messages:read']);

        return response()->json([
            'token' => $token->accessToken,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile-client', ['feed:read', 'projects:read', 'messages:read']);

        return response()->json([
            'token' => $token->accessToken,
            'user' => $user,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Token revoked']);
    }
}
