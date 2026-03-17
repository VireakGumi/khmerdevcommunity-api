@extends('layouts.app', ['title' => 'Messages', 'pageTitle' => 'Messages', 'shell' => 'mobile'])

@section('content')
    <div class="space-y-4">
        @foreach ($threads as $thread)
            @php($partner = $thread->sender_id === $currentUser->id ? $thread->recipient : $thread->sender)
            <article class="panel-soft p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $partner->name }}</h3>
                        <p class="text-xs text-slate-500">{{ '@'.$partner->username }}</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $thread->sent_at->diffForHumans() }}</span>
                </div>
                <p class="mt-3 text-sm text-slate-300">{{ $thread->body }}</p>
            </article>
        @endforeach
    </div>
@endsection
