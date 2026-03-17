<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public User $reader,
        public ?int $lastReadMessageId,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversations.{$this->conversation->id}"),
            new PrivateChannel("users.{$this->reader->id}.inbox"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.conversation.read';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'read_by_user_id' => $this->reader->id,
            'last_read_message_id' => $this->lastReadMessageId,
        ];
    }
}
