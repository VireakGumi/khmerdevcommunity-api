<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:32'],
            'platform' => ['required', 'string', 'max:32'],
            'token' => ['required', 'string', 'max:2048'],
            'device_label' => ['nullable', 'string', 'max:120'],
            'metadata' => ['nullable', 'array'],
        ]);

        $subscription = PushSubscription::updateOrCreate(
            ['token_hash' => hash('sha256', $data['token'])],
            [
                'user_id' => $request->user()->id,
                'provider' => $data['provider'],
                'platform' => $data['platform'],
                'token' => $data['token'],
                'device_label' => $data['device_label'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'last_registered_at' => now(),
                'last_error_at' => null,
                'last_error_message' => null,
            ]
        );

        return response()->json($subscription, 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:2048'],
        ]);

        PushSubscription::query()
            ->where('user_id', $request->user()->id)
            ->where('token_hash', hash('sha256', $data['token']))
            ->delete();

        return response()->json(['deleted' => true]);
    }
}
