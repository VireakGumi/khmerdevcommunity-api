<?php

namespace App\Http\Controllers\Api;

use App\Events\ConversationRead;
use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\CommunityNotification;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Support\Messaging\MessagingPresenter;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = trim((string) $request->query('q'));

        $conversations = Conversation::query()
            ->where('type', 'direct')
            ->whereHas('participants', fn ($builder) => $builder->where('users.id', $user->id))
            ->when($query !== '', function ($builder) use ($query, $user) {
                $builder->whereHas('participants', function ($participantQuery) use ($query, $user) {
                    $participantQuery
                        ->where('users.id', '!=', $user->id)
                        ->where(function ($search) use ($query) {
                            $search
                                ->where('users.name', 'like', "%{$query}%")
                                ->orWhere('users.username', 'like', "%{$query}%");
                        });
                });
            })
            ->with(['participants', 'memberships', 'latestMessage.sender'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Conversation $conversation) => MessagingPresenter::conversationSummary($conversation, $user))
            ->values();

        return response()->json($conversations);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        abort_if((int) $data['recipient_id'] === (int) $user->id, 422, 'You cannot message yourself.');

        $conversation = $this->findOrCreateDirectConversation($user, User::query()->findOrFail($data['recipient_id']));
        $conversation->load(['participants', 'memberships', 'latestMessage.sender']);

        return response()->json(MessagingPresenter::conversationSummary($conversation, $user), 201);
    }

    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->isParticipant($conversation, $user), 403);

        $conversation->load(['participants', 'memberships', 'latestMessage.sender']);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderByDesc('sent_at')
            ->paginate(30)
            ->through(fn (Message $message) => MessagingPresenter::message($message, $user, $conversation));

        return response()->json([
            'conversation' => MessagingPresenter::conversationSummary($conversation, $user),
            'messages' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'has_more_pages' => $messages->hasMorePages(),
            ],
        ]);
    }

    public function send(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->isParticipant($conversation, $user), 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = DB::transaction(function () use ($conversation, $data, $user) {
            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body' => $data['body'],
                'sent_at' => now(),
            ]);

            $conversation->forceFill(['updated_at' => $message->sent_at])->saveQuietly();

            foreach ($conversation->participants()->where('users.id', '!=', $user->id)->get() as $recipient) {
                CommunityNotification::create([
                    'user_id' => $recipient->id,
                    'type' => 'message',
                    'title' => 'New direct message',
                    'body' => $user->name.' sent you a message.',
                    'action_url' => "/messages?conversation={$conversation->id}",
                    'sent_at' => now(),
                ]);
            }

            return $message;
        });

        $conversation->load(['participants', 'memberships', 'latestMessage.sender']);
        $message->load('sender');

        $this->broadcastSafely(new MessageSent($conversation, $message));

        return response()->json([
            'conversation' => MessagingPresenter::conversationSummary($conversation, $user),
            'message' => MessagingPresenter::message($message, $user, $conversation),
            'unread_total' => MessagingPresenter::totalUnreadCount($user),
        ], 201);
    }

    public function markRead(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        abort_unless($this->isParticipant($conversation, $user), 403);

        $latestIncoming = $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->latest('id')
            ->first();

        $membership = ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $lastReadMessageId = $latestIncoming?->id ?? $membership->last_read_message_id;

        $membership->update([
            'last_read_message_id' => $lastReadMessageId,
            'last_read_at' => $latestIncoming ? now() : $membership->last_read_at,
        ]);

        CommunityNotification::query()
            ->where('user_id', $user->id)
            ->where('type', 'message')
            ->where('action_url', "/messages?conversation={$conversation->id}")
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load(['participants', 'memberships', 'latestMessage.sender']);

        $this->broadcastSafely(new ConversationRead($conversation, $user, $lastReadMessageId));

        return response()->json([
            'conversation_id' => $conversation->id,
            'unread_count' => MessagingPresenter::unreadCount($conversation, $user),
            'unread_total' => MessagingPresenter::totalUnreadCount($user),
            'last_read_message_id' => $lastReadMessageId,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => MessagingPresenter::totalUnreadCount($request->user()),
        ]);
    }

    private function isParticipant(Conversation $conversation, User $user): bool
    {
        return $conversation->participants()
            ->where('users.id', $user->id)
            ->exists();
    }

    private function findOrCreateDirectConversation(User $user, User $recipient): Conversation
    {
        $conversation = Conversation::query()
            ->where('type', 'direct')
            ->whereHas('participants', fn ($builder) => $builder->where('users.id', $user->id))
            ->whereHas('participants', fn ($builder) => $builder->where('users.id', $recipient->id))
            ->withCount('participants')
            ->get()
            ->first(fn (Conversation $item) => $item->participants_count === 2);

        if ($conversation) {
            return $conversation;
        }

        return DB::transaction(function () use ($recipient, $user) {
            $conversation = Conversation::create([
                'type' => 'direct',
                'created_by' => $user->id,
            ]);

            $conversation->participants()->attach([
                $user->id => ['joined_at' => now()],
                $recipient->id => ['joined_at' => now()],
            ]);

            return $conversation;
        });
    }

    private function broadcastSafely(object $event): void
    {
        try {
            broadcast($event)->toOthers();
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
