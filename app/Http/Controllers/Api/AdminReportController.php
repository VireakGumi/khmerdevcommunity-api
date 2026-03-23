<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentReport;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $type = $request->query('type');

        $query = ContentReport::query()
            ->with(['reporter', 'reportable'])
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($type && $type !== 'all') {
            $query->where('reportable_type', $this->resolveMorphClass($type));
        }

        $items = $query->take(100)->get()->map(function (ContentReport $report) {
            return [
                ...$report->toArray(),
                'reportable_label' => class_basename($report->reportable_type),
                'reportable_title' => $this->resolveReportableTitle($report->reportable),
                'reportable_path' => $this->resolveReportablePath($report->reportable),
            ];
        });

        return response()->json([
            'summary' => [
                'total_reports' => ContentReport::count(),
                'open_reports' => ContentReport::where('status', 'open')->count(),
                'resolved_reports' => ContentReport::where('status', 'resolved')->count(),
                'ignored_reports' => ContentReport::where('status', 'ignored')->count(),
            ],
            'items' => $items,
        ]);
    }

    public function update(Request $request, ContentReport $contentReport)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,resolved,ignored'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        $contentReport->forceFill([
            'status' => $validated['status'],
            'details' => $validated['details'] ?? $contentReport->details,
        ])->save();

        return response()->json($contentReport->fresh(['reporter', 'reportable']));
    }

    private function resolveMorphClass(string $type): ?string
    {
        return match ($type) {
            'post' => \App\Models\CommunityPost::class,
            'event' => \App\Models\CommunityEvent::class,
            'project' => \App\Models\Project::class,
            'job' => \App\Models\JobListing::class,
            'user' => \App\Models\User::class,
            default => null,
        };
    }

    private function resolveReportableTitle(mixed $reportable): string
    {
        if (! $reportable) {
            return 'Missing content';
        }

        return $reportable->title
            ?? $reportable->name
            ?? $reportable->company_name
            ?? $reportable->username
            ?? 'Untitled';
    }

    private function resolveReportablePath(mixed $reportable): ?string
    {
        if (! $reportable) {
            return null;
        }

        return match ($reportable::class) {
            \App\Models\CommunityPost::class => '/feed/'.$reportable->id,
            \App\Models\CommunityEvent::class => '/events/'.$reportable->id,
            \App\Models\Project::class => '/projects',
            \App\Models\JobListing::class => '/jobs/'.$reportable->slug,
            \App\Models\User::class => '/u/'.$reportable->username,
            default => null,
        };
    }
}
