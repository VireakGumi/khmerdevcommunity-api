<?php

namespace App\Support\Messaging;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;

class MessagingPresenter
{
    public static function conversationSummary(Conversation $conversation, User $viewer): array
    {
        $conversation->loadMissing([
            'participants',
            'memberships',
            'latestMessage.sender',
        ]);

        $partner = self::partner($conversation, $viewer);
        $latestMessage = $conversation->latestMessage;

        return [
            'id' => $conversation->id,
            'type' => $conversation->type,
            'partner' => $partner ? self::user($partner) : null,
            'participants' => $conversation->participants->map(fn (User $participant) => self::user($participant))->values(),
            'last_message' => $latestMessage ? self::message($latestMessage, $viewer, $conversation) : null,
            'last_message_at' => optional($latestMessage?->sent_at)->toISOString(),
            'unread_count' => self::unreadCount($conversation, $viewer),
            'updated_at' => optional($conversation->updated_at)->toISOString(),
        ];
    }

    public static function message(Message $message, User $viewer, ?Conversation $conversation = null): array
    {
        $message->loadMissing('sender');
        $conversation ??= $message->relationLoaded('conversation') ? $message->conversation : null;

        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender' => self::user($message->sender),
            'body' => $message->body,
            'sent_at' => optional($message->sent_at)->toISOString(),
            'edited_at' => optional($message->edited_at)->toISOString(),
            'is_mine' => $message->user_id === $viewer->id,
            'is_read' => self::isRead($message, $viewer, $conversation),
        ];
    }

    public static function unreadCount(Conversation $conversation, User $viewer): int
    {
        /** @var ConversationParticipant|null $membership */
        $membership = $conversation->memberships->firstWhere('user_id', $viewer->id)
            ?? $conversation->memberships()->where('user_id', $viewer->id)->first();

        $lastReadMessageId = $membership?->last_read_message_id ?? 0;

        return $conversation->messages()
            ->where('user_id', '!=', $viewer->id)
            ->where('id', '>', $lastReadMessageId)
            ->count();
    }

    public static function totalUnreadCount(User $viewer): int
    {
        $conversationIds = $viewer->conversations()->pluck('conversations.id');

        if ($conversationIds->isEmpty()) {
            return 0;
        }

        $memberships = ConversationParticipant::query()
            ->where('user_id', $viewer->id)
            ->whereIn('conversation_id', $conversationIds)
            ->get()
            ->keyBy('conversation_id');

        return Message::query()
            ->whereIn('conversation_id', $conversationIds)
            ->where('user_id', '!=', $viewer->id)
            ->get()
            ->filter(function (Message $message) use ($memberships): bool {
                $lastReadMessageId = $memberships[$message->conversation_id]?->last_read_message_id ?? 0;

                return $message->id > $lastReadMessageId;
            })
            ->count();
    }

    public static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatar_url' => $user->avatar_url,
            'headline' => $user->headline,
        ];
    }

    public static function partner(Conversation $conversation, User $viewer): ?User
    {
        return $conversation->participants->firstWhere('id', '!=', $viewer->id);
    }

    private static function isRead(Message $message, User $viewer, ?Conversation $conversation): bool
    {
        if ($message->user_id !== $viewer->id) {
            return true;
        }

        if (! $conversation) {
            return false;
        }

        $otherMembership = $conversation->memberships->firstWhere('user_id', '!=', $viewer->id)
            ?? $conversation->memberships()->where('user_id', '!=', $viewer->id)->first();

        return ($otherMembership?->last_read_message_id ?? 0) >= $message->id;
    }
}
