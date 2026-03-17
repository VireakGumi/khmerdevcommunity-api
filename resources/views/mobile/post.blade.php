@extends('layouts.app', ['title' => 'Mobile Post', 'pageTitle' => 'Post', 'shell' => 'mobile'])

@section('content')
    <form class="space-y-4">
        <div class="panel-soft p-4">
            <label class="mb-2 block text-sm text-slate-300" for="post-title">Title</label>
            <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="post-title" type="text" placeholder="Share a launch, lesson, or community update">
        </div>
        <div class="panel-soft p-4">
            <label class="mb-2 block text-sm text-slate-300" for="post-body">Body</label>
            <textarea class="min-h-40 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="post-body" placeholder="What are you building, learning, or shipping?"></textarea>
        </div>
        <div class="panel-soft p-4">
            <p class="text-sm text-slate-300">Topics</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($topics as $topic)
                    <span class="pill">{{ $topic }}</span>
                @endforeach
            </div>
        </div>
        <button class="w-full rounded-2xl bg-orange-500 px-5 py-3 font-semibold text-white" type="button">Publish update</button>
    </form>
@endsection
