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

        if ($query === '') {
            return response()->json([
                'query' => '',
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
            ->latest('published_at')
            ->take(8)
            ->get();

        $developers = User::query()
            ->withCount(['posts', 'projects', 'followers'])
            ->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->orWhere('headline', 'like', "%{$query}%")
                ->orWhere('skills', 'like', "%{$query}%"))
            ->orderBy('name')
            ->take(8)
            ->get();

        $projects = Project::query()
            ->with('user')
            ->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$query}%")
                ->orWhere('tagline', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%"))
            ->orderByDesc('stars_count')
            ->take(8)
            ->get();

        $events = CommunityEvent::query()
            ->with('host')
            ->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%")
                ->orWhere('city', 'like', "%{$query}%"))
            ->orderBy('starts_at')
            ->take(8)
            ->get();

        $jobs = JobListing::query()
            ->with('user')
            ->where('status', 'active')
            ->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$query}%")
                ->orWhere('company_name', 'like', "%{$query}%")
                ->orWhere('summary', 'like', "%{$query}%"))
            ->latest('published_at')
            ->take(8)
            ->get();

        return response()->json([
            'query' => $query,
            'posts' => $posts,
            'developers' => $developers,
            'projects' => $projects,
            'events' => $events,
            'jobs' => $jobs,
        ]);
    }
}
