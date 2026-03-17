<?php

use App\Models\User;
use App\Models\CommunityPost;
use App\Models\Conversation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

it('returns the community home payload', function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);

    $response = $this->getJson('/api/home');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'stats' => ['developers', 'projects', 'events', 'posts'],
            'featuredPost' => ['title', 'user' => ['name', 'username']],
            'featuredProjects',
            'featuredEvents',
            'developers',
        ]);
});

it('returns a public portfolio payload by username', function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);

    $response = $this->getJson('/api/developers/roeunvireak');

    $response
        ->assertOk()
        ->assertJsonPath('username', 'roeunvireak')
        ->assertJsonStructure([
            'portfolio_headline',
            'portfolio_summary',
            'social_links',
            'featured_work',
            'posts',
            'projects',
        ]);
});

it('authenticates with passport login', function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);
    app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client', 'users');

    $response = $this->postJson('/api/login', [
        'email' => 'chanvireak906@gmail.com',
        'password' => 'password',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'username', 'email'],
        ]);
});

it('authenticates with github oauth and redirects back to the spa', function () {
    app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client', 'users');

    $socialUser = (new SocialiteUser())->map([
        'id' => 'github-123',
        'nickname' => 'roeunvireak',
        'name' => 'Roeun Vireak',
        'email' => 'chanvireak906@gmail.com',
        'avatar' => 'https://avatars.githubusercontent.com/u/123?v=4',
        'html_url' => 'https://github.com/roeunvireak',
        'bio' => 'Laravel and Vue builder',
    ]);

    $provider = \Mockery::mock(Provider::class);
    $provider->shouldReceive('stateless')->once()->andReturnSelf();
    $provider->shouldReceive('user')->once()->andReturn($socialUser);

    Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

    $response = $this
        ->withSession(['oauth_redirect' => '/messages'])
        ->get('/oauth/github/callback');

    $response->assertRedirect();

    expect($response->headers->get('Location'))->toContain('/#/auth/callback?');
    expect($response->headers->get('Location'))->toContain('token=');
    expect($response->headers->get('Location'))->toContain(urlencode('/messages'));

    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();

    expect($user->auth_provider)->toBe('github');
    expect($user->auth_provider_id)->toBe('github-123');
});

it('updates an authenticated user portfolio profile', function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);

    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();

    Passport::actingAs($user, ['messages:read', 'projects:read', 'feed:read'], 'api');

    $response = $this->putJson('/api/me/profile', [
        'name' => 'Roeun Vireak Updated',
        'username' => 'roeunvireak',
        'headline' => 'Backend architect',
        'portfolio_headline' => 'Crafting developer platforms with Khmer character',
        'portfolio_summary' => 'Updated summary for a portfolio-style profile.',
        'location' => 'Phnom Penh',
        'company' => 'The Institute of Banking and Finance',
        'availability' => 'Open for community partnerships',
        'bio' => 'Updated bio.',
        'skills' => ['Laravel', 'Vue', 'Community'],
        'social_links' => [
            'github' => 'https://github.com/roeunvireak',
            'linkedin' => 'https://linkedin.com/in/roeunvireak',
            'portfolio' => 'https://khdev.community/@roeunvireak',
            'x' => null,
        ],
        'featured_work' => [
            [
                'title' => 'Khmer Dev Community',
                'description' => 'An upgraded community product.',
                'link' => 'https://khdev.community/projects/khmer-dev-community',
                'stack' => 'Laravel, Quasar',
            ],
        ],
        'profile_palette' => [
            'primary' => '#f97316',
            'secondary' => '#38bdf8',
            'surface' => '#0f172a',
        ],
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('name', 'Roeun Vireak Updated')
        ->assertJsonPath('portfolio_headline', 'Crafting developer platforms with Khmer character')
        ->assertJsonPath('featured_work.0.title', 'Khmer Dev Community');
});

it('creates posts, comments, likes, projects, and conversation messages for authenticated users', function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);

    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();
    $recipient = User::query()->where('email', 'ravy@khdev.community')->firstOrFail();
    $post = CommunityPost::query()->firstOrFail();

    Passport::actingAs($user, ['messages:read', 'projects:read', 'feed:read'], 'api');

    $createdPost = $this->postJson('/api/feed', [
        'title' => 'Community launch retrospective',
        'topic' => 'Community',
        'excerpt' => 'What worked in the first release and what needs deeper product thinking next.',
        'body' => 'We learned where the interaction loops need to be tighter.',
    ])->assertCreated();

    $this->postJson("/api/feed/{$post->id}/comments", [
        'body' => 'This post needs a stronger project CTA.',
    ])->assertCreated();

    $this->postJson("/api/feed/{$post->id}/like")
        ->assertOk()
        ->assertJsonPath('liked', true);

    $this->postJson('/api/projects', [
        'name' => 'Khmer OSS Radar',
        'tagline' => 'Track open-source projects from the Khmer dev ecosystem.',
        'summary' => 'A submission-based directory for Khmer-maintained repositories.',
        'repo_url' => 'https://github.com/example/khmer-oss-radar',
        'demo_url' => 'https://khdev.community/projects/khmer-oss-radar',
        'tech_stack' => ['Quasar', 'Laravel'],
        'looking_for_collaborators' => true,
    ])->assertCreated();

    $conversationResponse = $this->postJson('/api/conversations', [
        'recipient_id' => $recipient->id,
    ])->assertCreated();

    $conversationId = $conversationResponse->json('id');

    $this->postJson("/api/conversations/{$conversationId}/messages", [
        'body' => 'Can you review the new feed interactions?',
    ])->assertCreated();

    $createdPost->assertJsonPath('title', 'Community launch retrospective');
});

it('uploads an authenticated user profile image', function () {
    Storage::fake('public');
    $this->seed(\Database\Seeders\CommunitySeeder::class);

    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();

    Passport::actingAs($user, ['messages:read', 'projects:read', 'feed:read'], 'api');

    $response = $this->postJson('/api/me/avatar', [
        'avatar' => UploadedFile::fake()->image('avatar.png', 300, 300),
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('email', 'chanvireak906@gmail.com');

    expect($response->json('avatar_url'))->toContain('/storage/avatars/');
    Storage::disk('public')->assertExists(str_replace('/storage/', '', parse_url($response->json('avatar_url'), PHP_URL_PATH)));
});

it('supports follows, bookmarks, search, and settings', function () {
    $this->seed(\Database\Seeders\CommunitySeeder::class);

    $user = User::query()->where('email', 'chanvireak906@gmail.com')->firstOrFail();
    $target = User::query()->where('email', 'ravy@khdev.community')->firstOrFail();
    $post = CommunityPost::query()->where('slug', 'designing-a-familiar-feed')->firstOrFail();
    $project = \App\Models\Project::query()->where('slug', 'khmerjobs-api')->firstOrFail();

    Passport::actingAs($user, ['messages:read', 'projects:read', 'feed:read'], 'api');

    $this->postJson("/api/users/{$target->id}/follow")
        ->assertOk()
        ->assertJsonPath('following', true);

    $this->postJson("/api/feed/{$post->id}/bookmark")
        ->assertOk()
        ->assertJsonPath('saved', true);

    $this->postJson("/api/projects/{$project->id}/bookmark")
        ->assertOk()
        ->assertJsonPath('saved', true);

    $this->getJson('/api/me/bookmarks')
        ->assertOk()
        ->assertJsonCount(2, 'posts')
        ->assertJsonCount(2, 'projects');

    $this->getJson('/api/search?q=khmer')
        ->assertOk()
        ->assertJsonStructure([
            'query',
            'posts',
            'developers',
            'projects',
            'events',
        ]);

    $this->putJson('/api/me/settings', [
        'notification_preferences' => [
            'mentions' => true,
            'comments' => false,
            'follows' => true,
            'messages' => true,
            'events' => false,
        ],
        'privacy_settings' => [
            'show_email' => false,
            'show_location' => true,
            'allow_messages' => true,
        ],
    ])->assertOk()->assertJsonPath('notification_preferences.comments', false);
});
