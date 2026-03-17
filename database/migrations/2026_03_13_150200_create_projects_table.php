<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline');
            $table->text('summary');
            $table->string('repo_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->json('tech_stack')->nullable();
            $table->unsignedInteger('contributors_count')->default(1);
            $table->unsignedInteger('stars_count')->default(0);
            $table->string('status')->default('active');
            $table->boolean('looking_for_collaborators')->default(false);
            $table->date('launched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
