<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationIntent extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'currency',
        'channel',
        'status',
        'transfer_reference',
        'donor_name',
        'donor_email',
        'note',
        'proof_image_url',
        'confirmed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
