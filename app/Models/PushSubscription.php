<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'platform',
        'token',
        'token_hash',
        'device_label',
        'metadata',
        'last_registered_at',
        'last_sent_at',
        'last_error_at',
        'last_error_message',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'last_registered_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'last_error_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
