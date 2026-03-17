@extends('layouts.app', ['title' => 'Developer Profiles', 'pageTitle' => 'Developer Profiles'])

@section('content')
    <div class="grid gap-5 lg:grid-cols-2">
        @foreach ($developers as $developer)
            <article class="panel p-6">
                <div class="flex items-start gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-500 text-lg font-bold text-white">
                        {{ strtoupper(substr($developer->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold">{{ $developer->name }}</h2>
                                <p class="text-sm text-slate-400">{{ '@'.$developer->username }} · {{ $developer->location }}</p>
                            </div>
                            <span class="pill">{{ $developer->company ?? 'Independent' }}</span>
                        </div>
                        <p class="mt-3 text-slate-300">{{ $developer->headline }}</p>
                        <p class="mt-3 text-sm leading-7 text-slate-400">{{ $developer->bio }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($developer->skills ?? [] as $skill)
                                <span class="pill">{{ $skill }}</span>
                            @endforeach
                        </div>
                        <div class="mt-5 flex flex-wrap gap-4 text-sm text-slate-400">
                            <span>{{ $developer->posts_count }} posts</span>
                            <span>{{ $developer->projects_count }} projects</span>
                            <span>{{ $developer->hosted_events_count }} events</span>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endsection
