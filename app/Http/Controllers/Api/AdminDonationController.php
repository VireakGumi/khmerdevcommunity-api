<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationIntent;
use Illuminate\Http\Request;

class AdminDonationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = DonationIntent::query()->with('user')->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $items = $query->take(100)->get();

        return response()->json([
            'summary' => [
                'total_submissions' => DonationIntent::count(),
                'pending_reviews' => DonationIntent::whereIn('status', ['confirmation_submitted', 'initiated', 'pending'])->count(),
                'approved' => DonationIntent::where('status', 'approved')->count(),
                'rejected' => DonationIntent::where('status', 'rejected')->count(),
                'approved_amount' => (float) DonationIntent::where('status', 'approved')->sum('amount'),
            ],
            'items' => $items,
        ]);
    }

    public function update(Request $request, DonationIntent $donationIntent)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $donationIntent->forceFill([
            'status' => $validated['status'],
            'note' => $validated['note'] ?? $donationIntent->note,
        ])->save();

        return response()->json($donationIntent->fresh());
    }
}
