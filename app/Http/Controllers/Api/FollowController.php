<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();

        abort_if($actor->is($user), 422, 'You cannot follow yourself.');

        $actor->following()->syncWithoutDetaching([$user->id]);

        if (! $user->is($actor)) {
            CommunityNotification::create([
                'user_id' => $user->id,
                'type' => 'follow',
                'title' => 'New follower',
                'body' => $actor->name.' started following you.',
                'action_url' => '/developers',
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'following' => true,
            'followers_count' => $user->followers()->count(),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $request->user()->following()->detach($user->id);

        return response()->json([
            'following' => false,
            'followers_count' => $user->followers()->count(),
        ]);
    }
}
