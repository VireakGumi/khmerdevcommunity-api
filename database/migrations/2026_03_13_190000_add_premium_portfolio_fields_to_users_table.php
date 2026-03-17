<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('portfolio_plan', 20)->default('free')->after('portfolio_summary');
            $table->string('portfolio_cover', 255)->nullable()->after('portfolio_plan');
            $table->string('portfolio_booking_url', 2048)->nullable()->after('portfolio_cover');
            $table->json('portfolio_case_studies')->nullable()->after('portfolio_booking_url');
            $table->json('portfolio_testimonials')->nullable()->after('portfolio_case_studies');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'portfolio_plan',
                'portfolio_cover',
                'portfolio_booking_url',
                'portfolio_case_studies',
                'portfolio_testimonials',
            ]);
        });
    }
};
