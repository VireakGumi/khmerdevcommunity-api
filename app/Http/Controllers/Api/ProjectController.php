<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId($request);

        $projects = Project::query()
            ->with('user')
            ->withCount('bookmarks')
            ->orderByDesc('stars_count')
            ->get()
            ->map(fn (Project $project) => $this->serializeProject($project, $currentUserId));

        return response()->json($projects);
    }

    public function show(Request $request, Project $project): JsonResponse
    {
        $project->load('user')->loadCount('bookmarks');

        return response()->json($this->serializeProject($project, $this->resolveCurrentUserId($request)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'repo_url' => ['nullable', 'url', 'max:2048'],
            'demo_url' => ['nullable', 'url', 'max:2048'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:50'],
            'looking_for_collaborators' => ['boolean'],
        ]);

        $project = Project::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(6)),
            'tagline' => $data['tagline'],
            'summary' => $data['summary'],
            'repo_url' => $data['repo_url'] ?? null,
            'demo_url' => $data['demo_url'] ?? null,
            'tech_stack' => $data['tech_stack'] ?? [],
            'contributors_count' => 1,
            'stars_count' => 0,
            'status' => 'new',
            'looking_for_collaborators' => $data['looking_for_collaborators'] ?? true,
            'launched_at' => now()->toDateString(),
        ]);

        return response()->json($this->serializeProject($project->load('user')->loadCount('bookmarks'), $request->user()->id), 201);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        abort_unless($project->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'repo_url' => ['nullable', 'url', 'max:2048'],
            'demo_url' => ['nullable', 'url', 'max:2048'],
            'tech_stack' => ['nullable', 'array'],
            'tech_stack.*' => ['string', 'max:50'],
            'looking_for_collaborators' => ['boolean'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        $project->update([
            'name' => $data['name'],
            'tagline' => $data['tagline'],
            'summary' => $data['summary'],
            'repo_url' => $data['repo_url'] ?? null,
            'demo_url' => $data['demo_url'] ?? null,
            'tech_stack' => $data['tech_stack'] ?? [],
            'looking_for_collaborators' => $data['looking_for_collaborators'] ?? false,
            'status' => $data['status'] ?? $project->status,
        ]);

        return response()->json($this->serializeProject($project->fresh()->load('user')->loadCount('bookmarks'), $request->user()->id));
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        abort_unless($project->user_id === $request->user()->id, 403);

        $project->delete();

        return response()->json(['deleted' => true]);
    }

    private function resolveCurrentUserId(Request $request): ?int
    {
        try {
            return $request->user('api')?->id;
        } catch (Throwable) {
            return null;
        }
    }

    private function serializeProject(Project $project, ?int $currentUserId): array
    {
        return [
            ...$project->toArray(),
            'is_saved' => $currentUserId
                ? $project->bookmarks()->where('user_id', $currentUserId)->exists()
                : false,
            'is_owner' => $currentUserId ? $project->user_id === $currentUserId : false,
        ];
    }
}
