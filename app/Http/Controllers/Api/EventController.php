<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\CommunityEvent;
use App\Models\CommunityNotification;
use App\Services\MediaUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class EventController extends Controller
{
    public function __construct(private readonly MediaUploadService $mediaUploadService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $currentUserId = $this->resolveCurrentUserId($request);
        $status = $request->query('status');
        $search = trim((string) $request->query('q', ''));

        $query = CommunityEvent::query()
            ->with(['host', 'comments.user'])
            ->withCount('bookmarks')
            ->orderBy('starts_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$search}%")
                ->orWhere('summary', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%"));
        }

        $events = $query->paginate(12)->through(fn (CommunityEvent $event) => $this->serializeEvent($event, $currentUserId));

        return response()->json($events);
    }

    public function show(Request $request, CommunityEvent $event): JsonResponse
    {
        $event->load(['host', 'comments.user', 'responses.user'])->loadCount('bookmarks');

        return response()->json($this->serializeEvent($event, $this->resolveCurrentUserId($request)));
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $data = $request->validated();

        $event = CommunityEvent::create([
            'host_id' => $request->user()->id,
            'title' => $data['title'],
            'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(6)),
            'summary' => $data['summary'],
            'details' => $data['details'],
            'format' => $data['format'],
            'status' => $data['status'] ?? ($data['publish'] ?? false ? 'upcoming' : 'draft'),
            'workshop_type' => $data['workshop_type'] ?? null,
            'venue' => $data['venue'] ?? '',
            'city' => $data['city'] ?? '',
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'capacity' => $data['capacity'] ?? 100,
            'registration_url' => $data['registration_url'] ?? null,
            'thumbnail_url' => $request->hasFile('thumbnail') ? $this->mediaUploadService->storeImage($request->file('thumbnail'), 'events') : null,
            'organizer_name' => $data['organizer_name'] ?? $request->user()->name,
            'organizer_url' => $data['organizer_url'] ?? null,
            'is_featured' => $data['is_featured'] ?? false,
            'published_at' => ($data['publish'] ?? false) ? now() : null,
        ]);

        $event->load('host')->loadCount('bookmarks');

        return response()->json($this->serializeEvent($event, $request->user()->id), 201);
    }

    public function update(UpdateEventRequest $request, CommunityEvent $event): JsonResponse
    {
        abort_unless($event->host_id === $request->user()->id, 403);
        $data = $request->validated();

        $event->update([
            'title' => $data['title'] ?? $event->title,
            'summary' => $data['summary'] ?? $event->summary,
            'details' => $data['details'] ?? $event->details,
            'format' => $data['format'] ?? $event->format,
            'status' => $data['status'] ?? $event->status,
            'workshop_type' => $data['workshop_type'] ?? $event->workshop_type,
            'venue' => $data['venue'] ?? $event->venue,
            'city' => $data['city'] ?? $event->city,
            'starts_at' => $data['starts_at'] ?? $event->starts_at,
            'ends_at' => $data['ends_at'] ?? $event->ends_at,
            'capacity' => $data['capacity'] ?? $event->capacity,
            'registration_url' => $data['registration_url'] ?? $event->registration_url,
            'thumbnail_url' => $request->hasFile('thumbnail') ? $this->mediaUploadService->storeImage($request->file('thumbnail'), 'events') : $event->thumbnail_url,
            'organizer_name' => $data['organizer_name'] ?? $event->organizer_name,
            'organizer_url' => $data['organizer_url'] ?? $event->organizer_url,
            'is_featured' => $data['is_featured'] ?? $event->is_featured,
            'published_at' => array_key_exists('publish', $data) ? ($data['publish'] ? now() : null) : $event->published_at,
        ]);

        $event->load('host')->loadCount('bookmarks');

        return response()->json($this->serializeEvent($event, $request->user()->id));
    }

    public function destroy(Request $request, CommunityEvent $event): JsonResponse
    {
        abort_unless($event->host_id === $request->user()->id, 403);

        $event->delete();

        return response()->json(['deleted' => true]);
    }

    public function respond(Request $request, CommunityEvent $event): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:interested,attending'],
        ]);

        $response = $event->responses()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['status' => $data['status']]
        );

        $event->forceFill([
            'interested_count' => $event->responses()->where('status', 'interested')->count(),
            'attendee_count' => $event->responses()->where('status', 'attending')->count(),
        ])->save();

        if ($event->host_id !== $request->user()->id) {
            CommunityNotification::create([
                'user_id' => $event->host_id,
                'type' => 'event',
                'title' => 'New RSVP on your event',
                'body' => $request->user()->name.' marked "'.$event->title.'" as '.$response->status.'.',
                'action_url' => '/events',
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'status' => $response->status,
            'interested_count' => $event->interested_count,
            'attendee_count' => $event->attendee_count,
        ]);
    }

    public function comment(Request $request, CommunityEvent $event): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1200'],
        ]);

        $comment = $event->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ])->load('user');

        return response()->json($comment, 201);
    }

    public function report(Request $request, CommunityEvent $event): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $event->reports()->create([
            'reporter_id' => $request->user()->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        return response()->json(['reported' => true], 201);
    }

    private function serializeEvent(CommunityEvent $event, ?int $currentUserId): array
    {
        $response = $currentUserId ? $event->responses()->where('user_id', $currentUserId)->first() : null;

        return [
            ...$event->toArray(),
            'response_status' => $response?->status,
            'is_saved' => $currentUserId ? $event->bookmarks()->where('user_id', $currentUserId)->exists() : false,
            'is_owner' => $currentUserId ? $event->host_id === $currentUserId : false,
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
