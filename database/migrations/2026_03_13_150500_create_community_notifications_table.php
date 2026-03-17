<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('read_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_notifications');
    }
};
