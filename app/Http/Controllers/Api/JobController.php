<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\CommunityNotification;
use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId($request);
        $search = trim((string) $request->query('q', ''));
        $type = $request->query('job_type');
        $mode = $request->query('work_mode');
        $level = $request->query('experience_level');
        $status = $request->query('status', 'active');

        $query = JobListing::query()
            ->with('user')
            ->withCount('bookmarks')
            ->when($status, fn ($builder) => $builder->where('status', $status))
            ->when($type, fn ($builder) => $builder->where('job_type', $type))
            ->when($mode, fn ($builder) => $builder->where('work_mode', $mode))
            ->when($level, fn ($builder) => $builder->where('experience_level', $level))
            ->when($search !== '', fn ($builder) => $builder->where(fn ($nested) => $nested
                ->where('title', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('summary', 'like', "%{$search}%")))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        $jobs = $query->paginate(12)->through(fn (JobListing $job) => $this->serializeJob($job, $currentUserId));

        return response()->json($jobs);
    }

    public function show(Request $request, JobListing $job): JsonResponse
    {
        $job->load('user')->loadCount('bookmarks');

        return response()->json($this->serializeJob($job, $this->resolveCurrentUserId($request)));
    }

    public function mine(Request $request): JsonResponse
    {
        $jobs = JobListing::query()
            ->withCount(['bookmarks', 'applications'])
            ->where('user_id', $request->user()->id)
            ->latest('published_at')
            ->latest('created_at')
            ->get()
            ->map(fn (JobListing $job) => $this->serializeJob($job, $request->user()->id));

        return response()->json($jobs);
    }

    public function applied(Request $request): JsonResponse
    {
        $applications = $request->user()
            ->jobApplications()
            ->with('job.user')
            ->latest()
            ->get()
            ->map(function ($application) use ($request) {
                return [
                    ...$application->toArray(),
                    'job' => $application->job ? $this->serializeJob($application->job, $request->user()->id) : null,
                ];
            });

        return response()->json($applications);
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $data = $request->validated();

        $job = JobListing::create([
            'user_id' => $request->user()->id,
            ...$data,
            'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(6)),
            'status' => $data['status'] ?? ($data['publish'] ?? false ? 'active' : 'draft'),
            'published_at' => ($data['publish'] ?? false) ? now() : null,
        ]);

        $job->load('user')->loadCount('bookmarks');

        return response()->json($this->serializeJob($job, $request->user()->id), 201);
    }

    public function update(UpdateJobRequest $request, JobListing $job): JsonResponse
    {
        abort_unless($job->user_id === $request->user()->id, 403);
        $data = $request->validated();

        $job->update([
            ...$data,
            'status' => $data['status'] ?? $job->status,
            'published_at' => array_key_exists('publish', $data) ? ($data['publish'] ? now() : null) : $job->published_at,
        ]);

        $job->load('user')->loadCount('bookmarks');

        return response()->json($this->serializeJob($job, $request->user()->id));
    }

    public function destroy(Request $request, JobListing $job): JsonResponse
    {
        abort_unless($job->user_id === $request->user()->id, 403);
        $job->delete();

        return response()->json(['deleted' => true]);
    }

    public function apply(Request $request, JobListing $job): JsonResponse
    {
        abort_if($job->user_id === $request->user()->id, 422, 'You cannot apply to your own job posting.');

        $data = $request->validate([
            'resume_url' => ['nullable', 'url', 'max:2048'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $application = $job->applications()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'status' => 'submitted',
                'resume_url' => $data['resume_url'] ?? null,
                'note' => $data['note'] ?? null,
            ]
        );

        $job->update(['applications_count' => $job->applications()->count()]);

        if ($job->user_id !== $request->user()->id) {
            CommunityNotification::create([
                'user_id' => $job->user_id,
                'type' => 'job',
                'title' => 'New job application',
                'body' => $request->user()->name.' applied to '.$job->title.'.',
                'action_url' => '/jobs/'.$job->slug,
                'sent_at' => now(),
            ]);
        }

        return response()->json($application, 201);
    }

    public function applicants(Request $request, JobListing $job): JsonResponse
    {
        abort_unless($job->user_id === $request->user()->id, 403);

        $applicants = $job->applications()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn ($application) => [
                ...$application->toArray(),
                'user' => [
                    'id' => $application->user?->id,
                    'name' => $application->user?->name,
                    'username' => $application->user?->username,
                    'headline' => $application->user?->headline,
                    'location' => $application->user?->location,
                    'avatar_url' => $application->user?->avatar_url,
                    'skills' => $application->user?->skills,
                ],
            ]);

        return response()->json([
            'job' => $this->serializeJob($job->loadCount(['bookmarks', 'applications']), $request->user()->id),
            'applicants' => $applicants,
        ]);
    }

    public function updateApplication(Request $request, JobListing $job, JobApplication $application): JsonResponse
    {
        abort_unless($job->user_id === $request->user()->id, 403);
        abort_unless($application->job_listing_id === $job->id, 404);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:submitted,reviewing,shortlisted,contacted,rejected,hired'],
        ]);

        $application->update([
            'status' => $data['status'],
        ]);

        if ($application->user_id !== $request->user()->id) {
            CommunityNotification::create([
                'user_id' => $application->user_id,
                'type' => 'job',
                'title' => 'Application updated',
                'body' => $job->company_name.' marked your '.$job->title.' application as '.$data['status'].'.',
                'action_url' => '/jobs/'.$job->slug,
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            ...$application->fresh()->toArray(),
            'user' => [
                'id' => $application->user?->id,
                'name' => $application->user?->name,
                'username' => $application->user?->username,
                'headline' => $application->user?->headline,
                'location' => $application->user?->location,
                'avatar_url' => $application->user?->avatar_url,
                'skills' => $application->user?->skills,
            ],
        ]);
    }

    public function report(Request $request, JobListing $job): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $job->reports()->create([
            'reporter_id' => $request->user()->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        return response()->json(['reported' => true], 201);
    }

    private function serializeJob(JobListing $job, ?int $currentUserId): array
    {
        return [
            ...$job->toArray(),
            'is_saved' => $currentUserId ? $job->bookmarks()->where('user_id', $currentUserId)->exists() : false,
            'is_applied' => $currentUserId ? $job->applications()->where('user_id', $currentUserId)->exists() : false,
            'is_owner' => $currentUserId ? $job->user_id === $currentUserId : false,
        ];
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
