@extends('layouts.app', ['title' => 'Khmer Dev Community', 'pageTitle' => 'Ship products, share knowledge, and meet Khmer developers'])

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1.4fr_0.8fr]">
        <div class="panel overflow-hidden p-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="pill">Community SaaS v1</span>
                <span class="pill">Passport Auth</span>
                <span class="pill">Web + Mobile Surfaces</span>
            </div>

            <h2 class="mt-6 max-w-3xl text-4xl font-bold leading-tight sm:text-5xl">
                A community product with GitHub structure, Dev.to publishing, and Discord energy.
            </h2>
            <p class="mt-5 max-w-2xl text-lg text-slate-300">
                Bring Khmer developers into one place to post progress, launch projects, host events, and stay connected from desktop and mobile.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a class="rounded-2xl bg-orange-500 px-5 py-3 font-semibold text-white" href="{{ auth()->check() ? route('feed') : route('register') }}">
                    {{ auth()->check() ? 'Open the community feed' : 'Create your developer account' }}
                </a>
                <a class="rounded-2xl border border-white/10 px-5 py-3 font-semibold text-slate-200" href="{{ auth()->check() ? route('mobile.feed') : route('login') }}">
                    {{ auth()->check() ? 'Preview mobile pages' : 'Sign in to continue' }}
                </a>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $label => $value)
                    <div class="metric-card">
                        <p class="text-sm capitalize text-slate-400">{{ $label }}</p>
                        <p class="mt-3 text-3xl font-bold">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel p-5">
                <p class="text-xs uppercase tracking-[0.3em] text-orange-300">Featured Post</p>
                <h3 class="mt-3 text-2xl font-bold">{{ $featuredPost?->title }}</h3>
                <p class="mt-3 text-sm text-slate-300">{{ $featuredPost?->excerpt }}</p>
                <div class="mt-5 flex items-center justify-between text-sm text-slate-400">
                    <span>{{ $featuredPost?->user?->name }}</span>
                    <span>{{ $featuredPost?->likes_count }} likes</span>
                </div>
            </div>

            <div class="panel p-5">
                <p class="text-xs uppercase tracking-[0.3em] text-orange-300">Top Builders</p>
                <div class="mt-4 space-y-3">
                    @foreach ($topBuilders as $builder)
                        <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                            <div>
                                <p class="font-semibold">{{ $builder->name }}</p>
                                <p class="text-sm text-slate-400">{{ $builder->headline }}</p>
                            </div>
                            <span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $builder->location }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="panel p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Live Projects</h3>
                <a class="text-sm text-orange-300" href="{{ auth()->check() ? route('projects') : route('register') }}">Explore all</a>
            </div>
            <div class="mt-5 space-y-4">
                @foreach ($featuredProjects as $project)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-lg font-semibold">{{ $project->name }}</h4>
                                <p class="mt-1 text-sm text-slate-300">{{ $project->tagline }}</p>
                            </div>
                            <span class="pill">{{ $project->status }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($project->tech_stack ?? [] as $tech)
                                <span class="pill">{{ $tech }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold">Upcoming Events</h3>
                <a class="text-sm text-orange-300" href="{{ auth()->check() ? route('events') : route('register') }}">See schedule</a>
            </div>
            <div class="mt-5 space-y-4">
                @foreach ($featuredEvents as $event)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-lg font-semibold">{{ $event->title }}</h4>
                                <p class="mt-1 text-sm text-slate-300">{{ $event->summary }}</p>
                            </div>
                            <span class="pill">{{ $event->format }}</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-sm text-slate-400">
                            <span>{{ $event->city }} · {{ $event->venue }}</span>
                            <span>{{ $event->starts_at->format('M d, H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
