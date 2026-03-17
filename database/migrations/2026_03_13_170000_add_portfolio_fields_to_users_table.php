<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('availability')->nullable()->after('company');
            $table->string('portfolio_headline')->nullable()->after('availability');
            $table->text('portfolio_summary')->nullable()->after('portfolio_headline');
            $table->json('social_links')->nullable()->after('portfolio_summary');
            $table->json('featured_work')->nullable()->after('social_links');
            $table->json('profile_palette')->nullable()->after('featured_work');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'availability',
                'portfolio_headline',
                'portfolio_summary',
                'social_links',
                'featured_work',
                'profile_palette',
            ]);
        });
    }
};
