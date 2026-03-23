<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'auth_provider',
        'auth_provider_id',
        'auth_provider_meta',
        'is_admin',
        'headline',
        'location',
        'bio',
        'avatar_url',
        'company',
        'skills',
        'availability',
        'portfolio_headline',
        'portfolio_summary',
        'portfolio_plan',
        'portfolio_cover',
        'portfolio_booking_url',
        'portfolio_case_studies',
        'portfolio_testimonials',
        'work_experience',
        'education_history',
        'certifications',
        'achievements',
        'social_links',
        'featured_work',
        'profile_palette',
        'notification_preferences',
        'privacy_settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'auth_provider_meta' => 'array',
            'skills' => 'array',
            'social_links' => 'array',
            'featured_work' => 'array',
            'profile_palette' => 'array',
            'portfolio_case_studies' => 'array',
            'portfolio_testimonials' => 'array',
            'work_experience' => 'array',
            'education_history' => 'array',
            'certifications' => 'array',
            'achievements' => 'array',
            'notification_preferences' => 'array',
            'privacy_settings' => 'array',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(CommunityPost::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function hostedEvents(): HasMany
    {
        return $this->hasMany(CommunityEvent::class, 'host_id');
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(DirectMessage::class, 'recipient_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(CommunityNotification::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function postLikes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function contentReports(): HasMany
    {
        return $this->hasMany(ContentReport::class, 'reporter_id');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(ContentReport::class, 'reportable');
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function conversationMemberships(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->using(ConversationParticipant::class)
            ->withPivot(['id', 'last_read_message_id', 'last_read_at', 'joined_at'])
            ->withTimestamps();
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
