<?php

use App\Events\ConversationRead;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;

beforeEach(function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);
});

it('creates or returns a direct conversation', function () {
    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();
    $recipient = User::query()->where('email', 'ravy@khdev.community')->firstOrFail();

    Passport::actingAs($user, ['feed:read'], 'api');

    $response = $this->postJson('/api/conversations', [
        'recipient_id' => $recipient->id,
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'partner' => ['id', 'name', 'username'],
            'unread_count',
        ]);

    expect(Conversation::query()->count())->toBeGreaterThan(0);
});

it('lists conversations ordered by latest activity and supports opening a thread', function () {
    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();

    Passport::actingAs($user, ['feed:read'], 'api');

    $list = $this->getJson('/api/conversations')
        ->assertOk()
        ->assertJsonStructure([
            '*' => ['id', 'partner', 'last_message', 'unread_count'],
        ]);

    $conversationId = $list->json('0.id');

    $this->getJson("/api/conversations/{$conversationId}")
        ->assertOk()
        ->assertJsonStructure([
            'conversation' => ['id', 'partner', 'last_message'],
            'messages',
            'meta' => ['current_page', 'has_more_pages'],
        ]);
});

it('sends a message, increments unread counts, and marks a conversation as read', function () {
    Event::fake([MessageSent::class, ConversationRead::class]);

    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();
    $recipient = User::query()->where('email', 'ravy@khdev.community')->firstOrFail();

    Passport::actingAs($user, ['feed:read'], 'api');

    $conversationId = $this->postJson('/api/conversations', [
        'recipient_id' => $recipient->id,
    ])->json('id');

    $send = $this->postJson("/api/conversations/{$conversationId}/messages", [
        'body' => 'Realtime inbox test message.',
    ]);

    $send
        ->assertCreated()
        ->assertJsonPath('message.body', 'Realtime inbox test message.');

    Event::assertDispatched(MessageSent::class);

    Passport::actingAs($recipient, ['feed:read'], 'api');

    $conversation = $this->getJson("/api/conversations/{$conversationId}")
        ->assertOk()
        ->assertJsonPath('conversation.unread_count', 1);

    $this->postJson("/api/conversations/{$conversationId}/read")
        ->assertOk()
        ->assertJsonPath('unread_count', 0);

    Event::assertDispatched(ConversationRead::class);
});

it('forbids access to conversations for non-participants', function () {
    $owner = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();
    $recipient = User::query()->where('email', 'ravy@khdev.community')->firstOrFail();
    $outsider = User::query()->where('email', 'nita@khdev.community')->firstOrFail();

    Passport::actingAs($owner, ['feed:read'], 'api');
    $conversationId = $this->postJson('/api/conversations', [
        'recipient_id' => $recipient->id,
    ])->json('id');

    Passport::actingAs($outsider, ['feed:read'], 'api');

    $this->getJson("/api/conversations/{$conversationId}")
        ->assertForbidden();
});
