<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Models\CommunityNotification;
use App\Models\CommunityPost;
use App\Models\DirectMessage;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function feed(): View
    {
        return view('web.feed', [
            'posts' => CommunityPost::with('user')->latest('published_at')->get(),
            'trendingProjects' => Project::with('user')->orderByDesc('stars_count')->take(3)->get(),
            'upcomingEvents' => CommunityEvent::with('host')->orderBy('starts_at')->take(3)->get(),
        ]);
    }

    public function projects(): View
    {
        return view('web.projects', [
            'projects' => Project::with('user')->orderByDesc('stars_count')->get(),
        ]);
    }

    public function events(): View
    {
        return view('web.events', [
            'events' => CommunityEvent::with('host')->orderBy('starts_at')->get(),
        ]);
    }

    public function profiles(): View
    {
        return view('web.profiles', [
            'developers' => User::query()->withCount(['posts', 'projects', 'hostedEvents'])->orderBy('name')->get(),
        ]);
    }

    public function mobileFeed(): View
    {
        return view('mobile.feed', [
            'posts' => CommunityPost::with('user')->latest('published_at')->take(8)->get(),
        ]);
    }

    public function mobilePost(): View
    {
        return view('mobile.post', [
            'topics' => ['Laravel', 'Vue', 'Flutter', 'AI', 'DevOps', 'Khmer OSS'],
        ]);
    }

    public function mobileNotifications(Request $request): View
    {
        $user = $request->user() ?? User::query()->firstOrFail();

        return view('mobile.notifications', [
            'notifications' => CommunityNotification::query()
                ->where('user_id', $user->id)
                ->latest('sent_at')
                ->get(),
        ]);
    }

    public function mobileProfile(Request $request): View
    {
        $user = $request->user() ?? User::query()->with(['projects', 'posts'])->firstOrFail();
        $user->loadMissing(['projects', 'posts']);

        return view('mobile.profile', ['developer' => $user]);
    }

    public function mobileMessages(Request $request): View
    {
        $user = $request->user() ?? User::query()->firstOrFail();

        $threads = DirectMessage::query()
            ->with(['sender', 'recipient'])
            ->where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderByDesc('sent_at')
            ->get()
            ->groupBy(fn (DirectMessage $message) => $message->sender_id === $user->id ? $message->recipient_id : $message->sender_id)
            ->map(fn ($messages) => $messages->first());

        return view('mobile.messages', [
            'threads' => $threads,
            'currentUser' => $user,
        ]);
    }
}
