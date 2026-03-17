<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommunityPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'visibility',
        'title',
        'slug',
        'excerpt',
        'body',
        'media',
        'link_url',
        'link_label',
        'shareable_type',
        'shareable_id',
        'topic',
        'reading_time',
        'likes_count',
        'comments_count',
        'pinned',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'pinned' => 'boolean',
            'published_at' => 'datetime',
            'media' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class, 'post_id')->latest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(ContentReport::class, 'reportable');
    }
}
