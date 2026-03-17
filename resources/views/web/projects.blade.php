@extends('layouts.app', ['title' => 'Projects', 'pageTitle' => 'Projects'])

@section('content')
    <div class="grid gap-5 lg:grid-cols-2">
        @foreach ($projects as $project)
            <article class="panel p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $project->name }}</h2>
                        <p class="mt-2 text-slate-300">{{ $project->tagline }}</p>
                    </div>
                    <span class="pill">{{ $project->status }}</span>
                </div>
                <p class="mt-4 text-sm leading-7 text-slate-400">{{ $project->summary }}</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($project->tech_stack ?? [] as $tech)
                        <span class="pill">{{ $tech }}</span>
                    @endforeach
                </div>
                <div class="mt-5 flex items-center justify-between text-sm text-slate-400">
                    <span>By {{ $project->user->name }}</span>
                    <span>{{ $project->contributors_count }} contributors · {{ $project->stars_count }} stars</span>
                </div>
            </article>
        @endforeach
    </div>
@endsection
