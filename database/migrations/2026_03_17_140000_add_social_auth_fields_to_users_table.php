<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('auth_provider', 32)->nullable()->after('password');
            $table->string('auth_provider_id', 191)->nullable()->after('auth_provider');
            $table->json('auth_provider_meta')->nullable()->after('auth_provider_id');

            $table->index(['auth_provider', 'auth_provider_id'], 'users_auth_provider_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_auth_provider_lookup');
            $table->dropColumn(['auth_provider', 'auth_provider_id', 'auth_provider_meta']);
        });
    }
};
