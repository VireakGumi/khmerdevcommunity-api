<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('work_experience')->nullable()->after('portfolio_testimonials');
            $table->json('education_history')->nullable()->after('work_experience');
            $table->json('certifications')->nullable()->after('education_history');
            $table->json('achievements')->nullable()->after('certifications');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'work_experience',
                'education_history',
                'certifications',
                'achievements',
            ]);
        });
    }
};
