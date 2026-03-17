<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityEvent;
use App\Models\CommunityPost;
use App\Models\JobListing;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:post,event,project,job,user'],
            'id' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:100'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $model = match ($data['type']) {
            'post' => CommunityPost::findOrFail($data['id']),
            'event' => CommunityEvent::findOrFail($data['id']),
            'project' => Project::findOrFail($data['id']),
            'job' => JobListing::findOrFail($data['id']),
            'user' => User::findOrFail($data['id']),
        };

        $model->reports()->create([
            'reporter_id' => $request->user()->id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
        ]);

        return response()->json(['reported' => true], 201);
    }
}
