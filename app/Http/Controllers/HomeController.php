<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use App\Models\CommunityPost;
use App\Models\Project;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'stats' => [
                'developers' => User::count(),
                'projects' => Project::count(),
                'events' => CommunityEvent::count(),
                'posts' => CommunityPost::count(),
            ],
            'featuredPost' => CommunityPost::with('user')->latest('published_at')->first(),
            'featuredProjects' => Project::with('user')->latest('launched_at')->take(3)->get(),
            'featuredEvents' => CommunityEvent::with('host')->orderBy('starts_at')->take(3)->get(),
            'topBuilders' => User::query()->latest()->take(4)->get(),
        ]);
    }
}
