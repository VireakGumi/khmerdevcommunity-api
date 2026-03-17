<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'tagline',
        'summary',
        'repo_url',
        'demo_url',
        'tech_stack',
        'contributors_count',
        'stars_count',
        'status',
        'looking_for_collaborators',
        'launched_at',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'looking_for_collaborators' => 'boolean',
            'launched_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
