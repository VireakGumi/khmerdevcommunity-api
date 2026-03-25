<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityEvent;
use App\Models\CommunityPost;
use App\Models\JobListing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', 'relevance');

        if ($query === '') {
            return response()->json([
                'query' => '',
                'sort' => $sort,
                'posts' => [],
                'developers' => [],
                'projects' => [],
                'events' => [],
                'jobs' => [],
            ]);
        }

        $posts = CommunityPost::query()
            ->with('user')
            ->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$query}%")
                ->orWhere('excerpt', 'like', "%{$query}%")
                ->orWhere('topic', 'like', "%{$query}%"))
            ->when(
                $sort === 'popular',
                fn ($builder) => $builder->orderByDesc('likes_count')->orderByDesc('comments_count')->orderByDesc('published_at'),
                fn ($builder) => $builder->latest('published_at')
            )
            ->take(8)
            ->get();

        $developers = User::query()
            ->withCount(['posts', 'projects', 'followers'])
            ->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->orWhere('headline', 'like', "%{$query}%")
                ->orWhere('skills', 'like', "%{$query}%"))
            ->when(
                $sort === 'latest',
                fn ($builder) => $builder->latest('created_at'),
                fn ($builder) => $builder->orderByDesc('followers_count')->orderBy('name')
            )
            ->take(8)
            ->get();

        $projects = Project::query()
            ->with('user')
            ->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$query}%")
                ->orWhere('tagline', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%"))
            ->when(
                $sort === 'latest',
                fn ($builder) => $builder->latest('created_at'),
                fn ($builder) => $builder->orderByDesc('stars_count')->orderBy('name')
            )
            ->take(8)
            ->get();

        $events = CommunityEvent::query()
            ->with('host')
            ->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%")
                ->orWhere('city', 'like', "%{$query}%"))
            ->when(
                $sort === 'latest',
                fn ($builder) => $builder->orderByDesc('starts_at'),
                fn ($builder) => $builder->orderBy('starts_at')
            )
            ->take(8)
            ->get();

        $jobs = JobListing::query()
            ->with('user')
            ->where('status', 'active')
            ->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$query}%")
                ->orWhere('company_name', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%"))
            ->when(
                $sort === 'popular',
                fn ($builder) => $builder->orderByDesc('applications_count')->orderByDesc('published_at'),
                fn ($builder) => $builder->latest('published_at')
            )
            ->take(8)
            ->get();

        return response()->json([
            'query' => $query,
            'sort' => $sort,
            'posts' => $posts,
            'developers' => $developers,
            'projects' => $projects,
            'events' => $events,
            'jobs' => $jobs,
        ]);
    }
}
