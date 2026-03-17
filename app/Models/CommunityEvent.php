<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommunityEvent extends Model
{
    protected $fillable = [
        'host_id',
        'title',
        'slug',
        'summary',
        'details',
        'format',
        'venue',
        'city',
        'status',
        'workshop_type',
        'starts_at',
        'ends_at',
        'capacity',
        'attendee_count',
        'interested_count',
        'bookmarks_count',
        'registration_url',
        'thumbnail_url',
        'organizer_name',
        'organizer_url',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(EventResponse::class, 'event_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(EventComment::class, 'event_id')->latest();
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
