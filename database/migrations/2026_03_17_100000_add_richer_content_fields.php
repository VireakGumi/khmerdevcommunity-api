<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_posts', function (Blueprint $table) {
            $table->string('type', 50)->default('text')->after('user_id');
            $table->string('visibility', 50)->default('public')->after('type');
            $table->json('media')->nullable()->after('body');
            $table->string('link_url')->nullable()->after('media');
            $table->string('link_label')->nullable()->after('link_url');
            $table->nullableMorphs('shareable');
            $table->softDeletes();

            $table->index(['type', 'published_at']);
            $table->index(['visibility', 'published_at']);
        });

        Schema::table('post_comments', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('post_id')->constrained('post_comments')->nullOnDelete();
        });

        Schema::table('community_events', function (Blueprint $table) {
            $table->string('status', 50)->default('draft')->after('city');
            $table->string('workshop_type', 100)->nullable()->after('status');
            $table->string('thumbnail_url')->nullable()->after('registration_url');
            $table->string('organizer_name')->nullable()->after('thumbnail_url');
            $table->string('organizer_url')->nullable()->after('organizer_name');
            $table->timestamp('published_at')->nullable()->after('is_featured');
            $table->unsignedInteger('interested_count')->default(0)->after('attendee_count');
            $table->unsignedInteger('bookmarks_count')->default(0)->after('interested_count');

            $table->index(['status', 'starts_at']);
        });

        Schema::create('event_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('community_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 50);
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('event_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('community_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('company_logo_url')->nullable();
            $table->string('company_website')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary');
            $table->longText('description');
            $table->string('job_type', 50);
            $table->string('work_mode', 50);
            $table->string('experience_level', 50);
            $table->string('location')->nullable();
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('salary_currency', 10)->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('apply_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('applications_count')->default(0);
            $table->unsignedInteger('bookmarks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['job_type', 'work_mode']);
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 50)->default('submitted');
            $table->string('resume_url')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['job_listing_id', 'user_id']);
        });

        Schema::create('content_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('reportable');
            $table->string('reason', 100);
            $table->text('details')->nullable();
            $table->string('status', 50)->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_reports');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_listings');
        Schema::dropIfExists('event_comments');
        Schema::dropIfExists('event_responses');

        Schema::table('community_events', function (Blueprint $table) {
            $table->dropIndex(['status', 'starts_at']);
            $table->dropColumn([
                'status',
                'workshop_type',
                'thumbnail_url',
                'organizer_name',
                'organizer_url',
                'published_at',
                'interested_count',
                'bookmarks_count',
            ]);
        });

        Schema::table('post_comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropIndex(['type', 'published_at']);
            $table->dropIndex(['visibility', 'published_at']);
            $table->dropMorphs('shareable');
            $table->dropSoftDeletes();
            $table->dropColumn([
                'type',
                'visibility',
                'media',
                'link_url',
                'link_label',
            ]);
        });
    }
};
