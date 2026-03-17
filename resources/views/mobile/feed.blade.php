@extends('layouts.app', ['title' => 'Mobile Feed', 'pageTitle' => 'Feed', 'shell' => 'mobile'])

@section('content')
    <div class="space-y-4">
        <div class="panel-soft p-4">
            <p class="text-xs uppercase tracking-[0.3em] text-orange-300">Quick Scan</p>
            <h2 class="mt-2 text-xl font-bold">What Khmer devs are shipping today</h2>
        </div>

        @foreach ($posts as $post)
            <article class="panel-soft p-4">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>{{ '@'.$post->user->username }}</span>
                    <span>{{ $post->published_at?->diffForHumans() }}</span>
                </div>
                <h3 class="mt-3 text-lg font-semibold">{{ $post->title }}</h3>
                <p class="mt-2 text-sm text-slate-300">{{ $post->excerpt }}</p>
                <div class="mt-4 flex items-center gap-4 text-xs text-slate-400">
                    <span>{{ $post->likes_count }} likes</span>
                    <span>{{ $post->comments_count }} replies</span>
                    <span>{{ $post->topic }}</span>
                </div>
            </article>
        @endforeach
    </div>
@endsection
