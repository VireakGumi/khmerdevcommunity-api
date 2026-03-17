<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('headline')->nullable()->after('password');
            $table->string('location')->nullable()->after('headline');
            $table->text('bio')->nullable()->after('location');
            $table->string('avatar_url')->nullable()->after('bio');
            $table->string('company')->nullable()->after('avatar_url');
            $table->json('skills')->nullable()->after('company');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'headline',
                'location',
                'bio',
                'avatar_url',
                'company',
                'skills',
            ]);
        });
    }
};
