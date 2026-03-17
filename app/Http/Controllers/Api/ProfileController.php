<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ProfileController extends Controller
{
    public function show(string $username): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId(request());

        $user = User::query()
            ->with([
                'projects' => fn ($query) => $query->with('user')->withCount('bookmarks')->orderByDesc('stars_count'),
                'posts' => fn ($query) => $query
                    ->with(['user', 'comments' => fn ($commentQuery) => $commentQuery->with('user')->latest()->take(3)])
                    ->withCount(['likes', 'bookmarks'])
                    ->latest('published_at')
                    ->take(5),
            ])
            ->withCount(['posts', 'projects', 'hostedEvents', 'followers', 'following'])
            ->where('username', $username)
            ->firstOrFail();

        $projects = $user->projects->map(function ($project) use ($currentUserId) {
            return [
                ...$project->toArray(),
                'is_saved' => $currentUserId
                    ? $project->bookmarks()->where('user_id', $currentUserId)->exists()
                    : false,
            ];
        });

        $posts = $user->posts->map(function ($post) use ($currentUserId) {
            return [
                ...$post->toArray(),
                'is_liked' => $currentUserId
                    ? $post->likes()->where('user_id', $currentUserId)->exists()
                    : false,
                'is_saved' => $currentUserId
                    ? $post->bookmarks()->where('user_id', $currentUserId)->exists()
                    : false,
            ];
        });

        return response()->json([
            ...$user->toArray(),
            'projects' => $projects,
            'posts' => $posts,
            'is_following' => $currentUserId
                ? $user->followers()->where('follower_id', $currentUserId)->exists()
                : false,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId($request);

        $developers = User::query()
            ->withCount(['posts', 'projects', 'hostedEvents', 'followers'])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($currentUserId) {
                return [
                    ...$user->toArray(),
                    'is_following' => $currentUserId
                        ? $user->followers()->where('follower_id', $currentUserId)->exists()
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
}
