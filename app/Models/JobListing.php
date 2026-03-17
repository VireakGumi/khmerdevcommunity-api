<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_logo_url',
        'company_website',
        'title',
        'slug',
        'summary',
        'description',
        'job_type',
        'work_mode',
        'experience_level',
        'location',
        'salary_min',
        'salary_max',
        'salary_currency',
        'tech_stack',
        'apply_url',
        'contact_email',
        'expires_at',
        'status',
        'published_at',
        'applications_count',
        'bookmarks_count',
    ];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'expires_at' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
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
