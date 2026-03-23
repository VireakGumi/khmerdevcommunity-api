<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\CommunityNotification;
use App\Models\CommunityPost;
use App\Models\PostComment;
use App\Models\Project;
use App\Models\CommunityEvent;
use App\Services\MediaUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class PostController extends Controller
{
    public function __construct(private readonly MediaUploadService $mediaUploadService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $currentUser = $this->resolveCurrentUser($request);
        $currentUserId = $currentUser?->id;
        $tab = $request->query('tab', 'for-you');
        $type = $request->query('type');
        $topic = $request->query('topic');
        $search = trim((string) $request->query('q', ''));

        $query = CommunityPost::query()
            ->with([
                'user',
                'comments' => fn ($builder) => $builder->whereNull('parent_id')->with(['user', 'replies.user'])->latest()->limit(4),
            ])
            ->withCount(['likes', 'bookmarks'])
            ->whereNotNull('published_at');

        if ($type) {
            $query->where('type', $type);
        }

        if ($topic) {
            $query->where('topic', $topic);
        }

        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%"));
        }

        if ($tab === 'following' && $currentUserId) {
            $followingIds = $currentUser?->following()->pluck('users.id') ?? collect();
            $query->whereIn('user_id', $followingIds);
        } elseif ($tab === 'trending') {
            $query->orderByDesc('likes_count')->orderByDesc('comments_count')->orderByDesc('published_at');
        } else {
            $query->latest('published_at');
        }

        $posts = $query->paginate(12)->through(fn (CommunityPost $post) => $this->serializePost($post, $currentUserId));

        return response()->json($posts);
    }

    public function show(Request $request, CommunityPost $post): JsonResponse
    {
        $post->load([
            'user',
            'comments' => fn ($builder) => $builder->whereNull('parent_id')->with(['user', 'replies.user'])->latest(),
        ])->loadCount(['likes', 'bookmarks']);

        return response()->json($this->serializePost($post, $this->resolveCurrentUser($request)?->id));
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $media = $this->mediaUploadService->storeImages($request->file('images', []), 'feed');

        $post = CommunityPost::create([
            'user_id' => $request->user()->id,
            'type' => $data['type'] ?? ($media ? 'image' : 'text'),
            'visibility' => $data['visibility'] ?? 'public',
            'title' => $data['title'],
            'slug' => Str::slug($data['title']).'-'.Str::lower(Str::random(6)),
            'excerpt' => $data['excerpt'] ?: Str::limit(strip_tags($data['body']), 180),
            'body' => $data['body'],
            'media' => $media,
            'link_url' => $data['link_url'] ?? null,
            'link_label' => $data['link_label'] ?? null,
            'shareable_type' => $this->resolveShareableType($data['shareable_type'] ?? null),
            'shareable_id' => $data['shareable_id'] ?? null,
            'topic' => $data['topic'],
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($data['body'])) / 180)),
            'likes_count' => 0,
            'comments_count' => 0,
            'pinned' => false,
            'published_at' => now(),
        ]);

        $post->load(['user', 'comments.user'])->loadCount(['likes', 'bookmarks']);

        return response()->json($this->serializePost($post, $request->user()->id), 201);
    }

    public function update(UpdatePostRequest $request, CommunityPost $post): JsonResponse
    {
        abort_unless($post->user_id === $request->user()->id, 403);

        $data = $request->validated();
        $media = $request->hasFile('images')
            ? $this->mediaUploadService->storeImages($request->file('images', []), 'feed')
            : $post->media;

        $post->update([
            'type' => $data['type'] ?? $post->type,
            'visibility' => $data['visibility'] ?? $post->visibility,
            'title' => $data['title'] ?? $post->title,
            'excerpt' => $data['excerpt'] ?? $post->excerpt,
            'body' => $data['body'] ?? $post->body,
            'media' => $media,
            'link_url' => $data['link_url'] ?? $post->link_url,
            'link_label' => $data['link_label'] ?? $post->link_label,
            'topic' => $data['topic'] ?? $post->topic,
            'reading_time' => max(1, (int) ceil(str_word_count(strip_tags($data['body'] ?? $post->body)) / 180)),
        ]);

        $post->load(['user', 'comments.user'])->loadCount(['likes', 'bookmarks']);

        return response()->json($this->serializePost($post, $request->user()->id));
    }

    public function destroy(Request $request, CommunityPost $post): JsonResponse
    {
        abort_unless($post->user_id === $request->user()->id, 403);

        $post->delete();

        return response()->json(['deleted' => true]);
    }

    public function comment(Request $request, CommunityPost $post): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:1200'],
            'parent_id' => ['nullable', 'integer', 'exists:post_comments,id'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ])->load(['user', 'replies.user']);

        $post->increment('comments_count');

        if ($post->user_id !== $request->user()->id) {
            CommunityNotification::create([
                'user_id' => $post->user_id,
                'type' => 'comment',
                'title' => 'New comment on your post',
                'body' => $request->user()->name.' commented on "'.$post->title.'".',
                'action_url' => '/feed',
                'sent_at' => now(),
            ]);
        }

        return response()->json($comment, 201);
    }

    public function updateComment(Request $request, PostComment $comment): JsonResponse
    {
        abort_unless($comment->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1200'],
        ]);

        $comment->update([
            'body' => $data['body'],
        ]);

        return response()->json($comment->fresh()->load(['user', 'replies.user']));
    }

    public function destroyComment(Request $request, PostComment $comment): JsonResponse
    {
        abort_unless($comment->user_id === $request->user()->id, 403);

        $post = $comment->post;
        $removedCount = 1 + $comment->replies()->count();

        $comment->delete();

        if ($post) {
            $post->decrement('comments_count', min($removedCount, $post->comments_count));
        }

        return response()->json([
            'deleted' => true,
            'removed_count' => $removedCount,
        ]);
    }

    public function toggleLike(Request $request, CommunityPost $post): JsonResponse
    {
        $like = $post->likes()->where('user_id', $request->user()->id)->first();

        if ($like) {
            $like->delete();
            $post->decrement('likes_count');

            return response()->json([
                'liked' => false,
                'likes_count' => $post->fresh()->likes_count,
            ]);
        }

        $post->likes()->create([
            'user_id' => $request->user()->id,
            'created_at' => now(),
        ]);

        $post->increment('likes_count');

        if ($post->user_id !== $request->user()->id) {
            CommunityNotification::create([
                'user_id' => $post->user_id,
                'type' => 'like',
                'title' => 'Someone liked your post',
                'body' => $request->user()->name.' liked "'.$post->title.'".',
                'action_url' => '/feed',
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'liked' => true,
            'likes_count' => $post->fresh()->likes_count,
        ]);
    }

    public function report(Request $request, CommunityPost $post): JsonResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $post->reports()->create([
            'reporter_id' => $request->user()->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        return response()->json(['reported' => true], 201);
    }

    private function resolveCurrentUser(Request $request): mixed
    {
        try {
            return $request->user('api');
        } catch (Throwable) {
            return null;
        }
    }

    private function serializePost(CommunityPost $post, ?int $currentUserId): array
    {
        return [
            ...$post->toArray(),
            'is_liked' => $currentUserId ? $post->likes()->where('user_id', $currentUserId)->exists() : false,
            'is_saved' => $currentUserId ? $post->bookmarks()->where('user_id', $currentUserId)->exists() : false,
        ];
    }

    private function resolveShareableType(?string $type): ?string
    {
        return match ($type) {
            'project' => Project::class,
            'event' => CommunityEvent::class,
            default => null,
        };
    }
}
