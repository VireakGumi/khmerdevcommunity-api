<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityEvent;
use App\Models\CommunityPost;
use App\Models\JobListing;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $posts = CommunityPost::query()
            ->with('user')
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id))
            ->latest('published_at')
            ->get();

        $projects = Project::query()
            ->with('user')
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        $events = CommunityEvent::query()
            ->with('host')
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id))
            ->orderBy('starts_at')
            ->get();

        $jobs = JobListing::query()
            ->with('user')
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $user->id))
            ->latest('published_at')
            ->get();

        return response()->json([
            'posts' => $posts,
            'projects' => $projects,
            'events' => $events,
            'jobs' => $jobs,
        ]);
    }

    public function togglePost(Request $request, CommunityPost $post): JsonResponse
    {
        return response()->json($this->toggle($request, $post));
    }

    public function toggleProject(Request $request, Project $project): JsonResponse
    {
        return response()->json($this->toggle($request, $project));
    }

    public function toggleEvent(Request $request, CommunityEvent $event): JsonResponse
    {
        return response()->json($this->toggle($request, $event));
    }

    public function toggleJob(Request $request, JobListing $job): JsonResponse
    {
        return response()->json($this->toggle($request, $job));
    }

    protected function toggle(Request $request, Model $model): array
    {
        $bookmark = $model->bookmarks()->where('user_id', $request->user()->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $this->syncBookmarksCount($model);

            return ['saved' => false];
        }

        $model->bookmarks()->create([
            'user_id' => $request->user()->id,
        ]);

        $this->syncBookmarksCount($model);

        return ['saved' => true];
    }

    protected function syncBookmarksCount(Model $model): void
    {
        if (in_array('bookmarks_count', $model->getFillable(), true)) {
            $model->update(['bookmarks_count' => $model->bookmarks()->count()]);
        }
    }
}
