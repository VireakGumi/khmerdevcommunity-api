<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use App\Support\Messaging\MessagingPresenter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public Message $message,
    ) {
    }

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel("conversations.{$this->conversation->id}")];

        foreach ($this->conversation->participants as $participant) {
            $channels[] = new PrivateChannel("users.{$participant->id}.inbox");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->user_id,
                'body' => $this->message->body,
                'sent_at' => optional($this->message->sent_at)->toISOString(),
                'sender' => MessagingPresenter::user($this->message->sender),
            ],
            'participants' => $this->conversation->participants->map(fn ($participant) => MessagingPresenter::user($participant))->values(),
            'last_message_at' => optional($this->message->sent_at)->toISOString(),
        ];
    }
}
