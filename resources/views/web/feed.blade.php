@extends('layouts.app', ['title' => 'Community Feed', 'pageTitle' => 'Community Feed'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-5">
            @foreach ($posts as $post)
                <article class="panel p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="pill">{{ $post->topic }}</span>
                                @if ($post->pinned)
                                    <span class="pill">Pinned</span>
                                @endif
                            </div>
                            <h2 class="mt-4 text-2xl font-bold">{{ $post->title }}</h2>
                            <p class="mt-3 text-slate-300">{{ $post->excerpt }}</p>
                        </div>
                        <div class="text-right text-sm text-slate-400">
                            <p>{{ $post->user->name }}</p>
                            <p>{{ $post->published_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-wrap gap-4 text-sm text-slate-400">
                        <span>{{ $post->reading_time }} min read</span>
                        <span>{{ $post->likes_count }} likes</span>
                        <span>{{ $post->comments_count }} comments</span>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="space-y-5">
            <div class="panel p-5">
                <h3 class="text-lg font-bold">Trending Projects</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($trendingProjects as $project)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="font-semibold">{{ $project->name }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ $project->tagline }}</p>
                            <p class="mt-3 text-xs uppercase tracking-[0.2em] text-orange-300">{{ $project->stars_count }} stars</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel p-5">
                <h3 class="text-lg font-bold">Upcoming Events</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($upcomingEvents as $event)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="font-semibold">{{ $event->title }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ $event->city }} · {{ $event->starts_at->format('M d, H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
