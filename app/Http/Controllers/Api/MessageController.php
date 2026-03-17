<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityNotification;
use App\Models\DirectMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            DirectMessage::with(['sender', 'recipient'])
                ->where(fn ($query) => $query
                    ->where('sender_id', $request->user()->id)
                    ->orWhere('recipient_id', $request->user()->id))
                ->orderByDesc('sent_at')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message = DirectMessage::create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $data['recipient_id'],
            'body' => $data['body'],
            'sent_at' => now(),
        ])->load(['sender', 'recipient']);

        CommunityNotification::create([
            'user_id' => $data['recipient_id'],
            'type' => 'message',
            'title' => 'New direct message',
            'body' => $request->user()->name.' sent you a message.',
            'action_url' => '/m/messages',
            'sent_at' => now(),
        ]);

        return response()->json($message, 201);
    }
}
