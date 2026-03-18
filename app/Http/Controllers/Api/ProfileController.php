<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ProfileController extends Controller
{
    public function show(string $username): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId(request());
        $user = User::query()->where('username', $username)->firstOrFail();

        try {
            $user = $this->findDeveloper($username);

            $projects = $this->loadedRelation($user, 'projects')->map(function ($project) use ($currentUserId) {
                return [
                    ...$project->toArray(),
                    'is_saved' => $currentUserId
                        ? $this->bookmarksTableExists() && $project->bookmarks()->where('user_id', $currentUserId)->exists()
                        : false,
                ];
            });

            $posts = $this->loadedRelation($user, 'posts')->map(function ($post) use ($currentUserId) {
                return [
                    ...$post->toArray(),
                    'is_liked' => $currentUserId
                        ? $this->tableExists('post_likes') && $post->likes()->where('user_id', $currentUserId)->exists()
                        : false,
                    'is_saved' => $currentUserId
                        ? $this->bookmarksTableExists() && $post->bookmarks()->where('user_id', $currentUserId)->exists()
                        : false,
                ];
            });

            return response()->json([
                ...$user->toArray(),
                'projects' => $projects,
                'posts' => $posts,
                'is_following' => $currentUserId
                    ? $this->followsTableExists() && $user->followers()->where('follower_id', $currentUserId)->exists()
                    : false,
            ]);
        } catch (Throwable) {
            return response()->json([
                ...$user->toArray(),
                'projects' => [],
                'posts' => [],
                'posts_count' => $user->posts_count ?? 0,
                'projects_count' => $user->projects_count ?? 0,
                'hosted_events_count' => $user->hosted_events_count ?? 0,
                'followers_count' => $user->followers_count ?? 0,
                'following_count' => $user->following_count ?? 0,
                'is_following' => false,
            ]);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId($request);

        $developers = $this->developerIndexQuery()
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($currentUserId) {
                return [
                    ...$user->toArray(),
                    'is_following' => $currentUserId
                        ? $this->followsTableExists() && $user->followers()->where('follower_id', $currentUserId)->exists()
                        : false,
                ];
            });

        return response()->json($developers);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'headline' => ['nullable', 'string', 'max:255'],
            'portfolio_headline' => ['nullable', 'string', 'max:255'],
            'portfolio_summary' => ['nullable', 'string'],
            'portfolio_cover' => ['nullable', 'string', 'max:255'],
            'portfolio_booking_url' => ['nullable', 'url', 'max:2048'],
            'location' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'availability' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string'],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:50'],
            'social_links' => ['nullable', 'array'],
            'social_links.github' => ['nullable', 'url', 'max:2048'],
            'social_links.linkedin' => ['nullable', 'url', 'max:2048'],
            'social_links.portfolio' => ['nullable', 'url', 'max:2048'],
            'social_links.x' => ['nullable', 'url', 'max:2048'],
            'featured_work' => ['nullable', 'array', 'max:4'],
            'featured_work.*.title' => ['required', 'string', 'max:255'],
            'featured_work.*.description' => ['nullable', 'string', 'max:500'],
            'featured_work.*.link' => ['nullable', 'url', 'max:2048'],
            'featured_work.*.stack' => ['nullable', 'string', 'max:255'],
            'portfolio_case_studies' => ['nullable', 'array', 'max:3'],
            'portfolio_case_studies.*.title' => ['required', 'string', 'max:255'],
            'portfolio_case_studies.*.summary' => ['nullable', 'string', 'max:500'],
            'portfolio_case_studies.*.impact' => ['nullable', 'string', 'max:255'],
            'portfolio_case_studies.*.link' => ['nullable', 'url', 'max:2048'],
            'portfolio_testimonials' => ['nullable', 'array', 'max:3'],
            'portfolio_testimonials.*.name' => ['required', 'string', 'max:255'],
            'portfolio_testimonials.*.role' => ['nullable', 'string', 'max:255'],
            'portfolio_testimonials.*.quote' => ['nullable', 'string', 'max:500'],
            'work_experience' => ['nullable', 'array', 'max:8'],
            'work_experience.*.role' => ['required', 'string', 'max:255'],
            'work_experience.*.company' => ['required', 'string', 'max:255'],
            'work_experience.*.period' => ['nullable', 'string', 'max:255'],
            'work_experience.*.location' => ['nullable', 'string', 'max:255'],
            'work_experience.*.type' => ['nullable', 'string', 'max:100'],
            'work_experience.*.summary' => ['nullable', 'string', 'max:500'],
            'education_history' => ['nullable', 'array', 'max:6'],
            'education_history.*.school' => ['required', 'string', 'max:255'],
            'education_history.*.degree' => ['nullable', 'string', 'max:255'],
            'education_history.*.field' => ['nullable', 'string', 'max:255'],
            'education_history.*.period' => ['nullable', 'string', 'max:255'],
            'education_history.*.summary' => ['nullable', 'string', 'max:500'],
            'certifications' => ['nullable', 'array', 'max:8'],
            'certifications.*.name' => ['required', 'string', 'max:255'],
            'certifications.*.issuer' => ['nullable', 'string', 'max:255'],
            'certifications.*.issued_at' => ['nullable', 'string', 'max:100'],
            'certifications.*.credential_url' => ['nullable', 'url', 'max:2048'],
            'achievements' => ['nullable', 'array', 'max:8'],
            'achievements.*.title' => ['required', 'string', 'max:255'],
            'achievements.*.issuer' => ['nullable', 'string', 'max:255'],
            'achievements.*.year' => ['nullable', 'string', 'max:50'],
            'achievements.*.summary' => ['nullable', 'string', 'max:500'],
            'profile_palette' => ['nullable', 'array'],
            'profile_palette.primary' => ['nullable', 'string', 'max:20'],
            'profile_palette.secondary' => ['nullable', 'string', 'max:20'],
            'profile_palette.surface' => ['nullable', 'string', 'max:20'],
        ]);

        if ($user->portfolio_plan !== 'premium') {
            $data['portfolio_case_studies'] = [];
            $data['portfolio_testimonials'] = [];
            $data['portfolio_booking_url'] = null;
            $data['portfolio_cover'] = null;
        }

        $user->fill($data);
        $user->save();

        return response()->json($user->fresh());
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'avatar' => ['required', 'image', 'max:8192'],
        ]);

        $avatarPath = $data['avatar']->store('avatars', 'public');

        if ($user->avatar_url && str_contains($user->avatar_url, '/storage/avatars/')) {
            $storedPath = ltrim(parse_url($user->avatar_url, PHP_URL_PATH) ?? '', '/');
            $storedPath = preg_replace('#^storage/#', '', $storedPath);

            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
        }

        $user->forceFill([
            'avatar_url' => Storage::disk('public')->url($avatarPath),
        ])->save();

        return response()->json($user->fresh());
    }

    private function resolveCurrentUserId(Request $request): ?int
    {
        try {
            return $request->user('api')?->id;
        } catch (Throwable) {
            return null;
        }
    }

    private function findDeveloper(string $username): User
    {
        $query = $this->developerIndexQuery();

        if ($this->tableExists('projects')) {
            $query->with([
                'projects' => fn ($projectQuery) => $projectQuery
                    ->with('user')
                    ->when($this->bookmarksTableExists(), fn ($q) => $q->withCount('bookmarks'))
                    ->orderByDesc('stars_count'),
            ]);
        }

        if ($this->tableExists('community_posts')) {
            $query->with([
                'posts' => fn ($postQuery) => $postQuery
                    ->with([
                        'user',
                        'comments' => fn ($commentQuery) => $this->tableExists('post_comments')
                            ? $commentQuery->with('user')->latest()->take(3)
                            : $commentQuery,
                    ])
                    ->when($this->tableExists('post_likes'), fn ($q) => $q->withCount('likes'))
                    ->when($this->bookmarksTableExists(), fn ($q) => $q->withCount('bookmarks'))
                    ->latest('published_at')
                    ->take(5),
            ]);
        }

        try {
            return $query->where('username', $username)->firstOrFail();
        } catch (QueryException) {
            return User::query()->where('username', $username)->firstOrFail();
        }
    }

    private function developerIndexQuery()
    {
        $query = User::query();
        $withCounts = [];

        if ($this->tableExists('community_posts')) {
            $withCounts[] = 'posts';
        }

        if ($this->tableExists('projects')) {
            $withCounts[] = 'projects';
        }

        if ($this->tableExists('community_events') && $this->columnExists('community_events', 'host_id')) {
            $withCounts[] = 'hostedEvents';
        }

        if ($this->followsTableExists()) {
            $withCounts[] = 'followers';
            $withCounts[] = 'following';
        }

        if (! empty($withCounts)) {
            try {
                $query->withCount($withCounts);
            } catch (QueryException) {
                // Fall back to base user query if production schema is behind code.
            }
        }

        return $query;
    }

    private function loadedRelation(User $user, string $relation): Collection
    {
        return $user->relationLoaded($relation)
            ? $user->getRelation($relation)
            : collect();
    }

    private function followsTableExists(): bool
    {
        return $this->tableExists('follows');
    }

    private function bookmarksTableExists(): bool
    {
        return $this->tableExists('bookmarks');
    }

    private function tableExists(string $table): bool
    {
        static $cache = [];

        return $cache[$table] ??= Schema::hasTable($table);
    }

    private function columnExists(string $table, string $column): bool
    {
        static $cache = [];
        $key = $table.'.'.$column;

        return $cache[$key] ??= Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }
}
