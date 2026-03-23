<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function show()
    {
        $config = config('community_support.donation');

        return response()->json([
            'enabled' => (bool) ($config['enabled'] ?? false),
            'title' => $config['title'] ?? 'Support KhmerDevCommunity',
            'summary' => $config['summary'] ?? null,
            'currency' => $config['currency'] ?? 'USD',
            'khqr_payload' => $config['khqr_payload'] ?? '',
            'khqr_account_name' => $config['khqr_account_name'] ?? 'KhmerDevCommunity',
            'contact_email' => $config['contact_email'] ?? null,
            'tiers' => $config['tiers'] ?? [],
            'buckets' => $config['buckets'] ?? [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:1', 'max:999999.99'],
            'currency' => ['nullable', 'string', 'max:8'],
            'channel' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', 'string', 'max:32'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'metadata' => ['nullable', 'array'],
        ]);

        $intent = DonationIntent::create([
            ...$validated,
            'user_id' => $request->user()?->id,
            'currency' => $validated['currency'] ?? config('community_support.donation.currency', 'USD'),
            'channel' => $validated['channel'] ?? 'khqr',
            'status' => $validated['status'] ?? 'initiated',
        ]);

        return response()->json([
            'id' => $intent->id,
            'status' => $intent->status,
        ], 201);
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'currency' => ['nullable', 'string', 'max:8'],
            'channel' => ['nullable', 'string', 'max:32'],
            'transfer_reference' => ['nullable', 'string', 'max:255'],
            'donor_name' => ['nullable', 'string', 'max:255'],
            'donor_email' => ['nullable', 'email', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'proof_image' => ['required', 'image', 'max:8192'],
            'metadata' => ['nullable', 'array'],
        ]);

        $proofPath = $validated['proof_image']->store('donations', 'public');

        $intent = DonationIntent::create([
            'user_id' => $request->user()?->id,
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? config('community_support.donation.currency', 'USD'),
            'channel' => $validated['channel'] ?? 'khqr',
            'status' => 'confirmation_submitted',
            'transfer_reference' => $validated['transfer_reference'] ?? null,
            'donor_name' => $validated['donor_name'] ?? null,
            'donor_email' => $validated['donor_email'] ?? null,
            'note' => $validated['note'] ?? null,
            'proof_image_url' => Storage::disk('public')->url($proofPath),
            'confirmed_at' => now(),
            'metadata' => [
                ...($validated['metadata'] ?? []),
                'source' => data_get($validated, 'metadata.source', 'home_page'),
            ],
        ]);

        return response()->json([
            'id' => $intent->id,
            'status' => $intent->status,
            'proof_image_url' => $intent->proof_image_url,
        ], 201);
    }
}
