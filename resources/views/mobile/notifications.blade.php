@extends('layouts.app', ['title' => 'Notifications', 'pageTitle' => 'Notifications', 'shell' => 'mobile'])

@section('content')
    <div class="space-y-4">
        @foreach ($notifications as $notification)
            <article class="panel-soft p-4">
                <div class="flex items-center justify-between">
                    <span class="pill">{{ $notification->type }}</span>
                    <span class="text-xs text-slate-500">{{ $notification->sent_at->diffForHumans() }}</span>
                </div>
                <h3 class="mt-3 font-semibold">{{ $notification->title }}</h3>
                <p class="mt-2 text-sm text-slate-300">{{ $notification->body }}</p>
                <p class="mt-3 text-xs {{ $notification->read_at ? 'text-slate-500' : 'text-orange-300' }}">
                    {{ $notification->read_at ? 'Read' : 'Unread' }}
                </p>
            </article>
        @endforeach
    </div>
@endsection
