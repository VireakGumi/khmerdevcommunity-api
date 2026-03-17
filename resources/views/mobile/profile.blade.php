@extends('layouts.app', ['title' => 'Profile', 'pageTitle' => 'Profile', 'shell' => 'mobile'])

@section('content')
    <div class="space-y-4">
        <section class="panel-soft p-5">
            <div class="flex items-center gap-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-orange-500 text-2xl font-bold text-white">
                    {{ strtoupper(substr($developer->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $developer->name }}</h2>
                    <p class="text-sm text-slate-400">{{ '@'.$developer->username }} · {{ $developer->location }}</p>
                </div>
            </div>
            <p class="mt-4 text-sm text-slate-300">{{ $developer->headline }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($developer->skills ?? [] as $skill)
                    <span class="pill">{{ $skill }}</span>
                @endforeach
            </div>
        </section>

        <section class="grid grid-cols-3 gap-3">
            <div class="panel-soft p-4 text-center">
                <p class="text-xs text-slate-500">Posts</p>
                <p class="mt-2 text-2xl font-bold">{{ $developer->posts->count() }}</p>
            </div>
            <div class="panel-soft p-4 text-center">
                <p class="text-xs text-slate-500">Projects</p>
                <p class="mt-2 text-2xl font-bold">{{ $developer->projects->count() }}</p>
            </div>
            <div class="panel-soft p-4 text-center">
                <p class="text-xs text-slate-500">Company</p>
                <p class="mt-2 text-sm font-semibold">{{ $developer->company ?? 'Indie' }}</p>
            </div>
        </section>
    </div>
@endsection
