<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('direct');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'updated_at']);
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('last_read_message_id')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
            $table->index(['user_id', 'conversation_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('sent_at');
            $table->timestamp('edited_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['conversation_id', 'sent_at']);
            $table->index(['conversation_id', 'id']);
        });

        Schema::table('conversation_participants', function (Blueprint $table) {
            $table->foreign('last_read_message_id')->references('id')->on('messages')->nullOnDelete();
        });

        $this->migrateLegacyDirectMessages();
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }

    private function migrateLegacyDirectMessages(): void
    {
        if (! Schema::hasTable('direct_messages')) {
            return;
        }

        $conversationsByPair = [];

        DB::table('direct_messages')
            ->orderBy('sent_at')
            ->orderBy('id')
            ->get()
            ->each(function (object $legacy) use (&$conversationsByPair): void {
                $pair = collect([$legacy->sender_id, $legacy->recipient_id])->sort()->values()->implode(':');

                if (! array_key_exists($pair, $conversationsByPair)) {
                    $conversationId = DB::table('conversations')->insertGetId([
                        'type' => 'direct',
                        'created_by' => $legacy->sender_id,
                        'created_at' => $legacy->sent_at,
                        'updated_at' => $legacy->sent_at,
                    ]);

                    foreach (collect([$legacy->sender_id, $legacy->recipient_id])->unique() as $participantId) {
                        DB::table('conversation_participants')->insert([
                            'conversation_id' => $conversationId,
                            'user_id' => $participantId,
                            'joined_at' => $legacy->sent_at,
                            'created_at' => $legacy->sent_at,
                            'updated_at' => $legacy->sent_at,
                        ]);
                    }

                    $conversationsByPair[$pair] = $conversationId;
                }

                $conversationId = $conversationsByPair[$pair];
                $messageId = DB::table('messages')->insertGetId([
                    'conversation_id' => $conversationId,
                    'user_id' => $legacy->sender_id,
                    'body' => $legacy->body,
                    'sent_at' => $legacy->sent_at,
                    'created_at' => $legacy->sent_at,
                    'updated_at' => $legacy->sent_at,
                ]);

                if ($legacy->read_at) {
                    DB::table('conversation_participants')
                        ->where('conversation_id', $conversationId)
                        ->where('user_id', $legacy->recipient_id)
                        ->update([
                            'last_read_message_id' => $messageId,
                            'last_read_at' => $legacy->read_at,
                            'updated_at' => $legacy->read_at,
                        ]);
                }

                DB::table('conversations')
                    ->where('id', $conversationId)
                    ->update(['updated_at' => $legacy->sent_at]);
            });
    }
};
