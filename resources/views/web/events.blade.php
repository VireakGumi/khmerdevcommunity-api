@extends('layouts.app', ['title' => 'Events', 'pageTitle' => 'Events'])

@section('content')
    <div class="space-y-5">
        @foreach ($events as $event)
            <article class="panel p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="pill">{{ $event->format }}</span>
                            @if ($event->is_featured)
                                <span class="pill">Featured</span>
                            @endif
                        </div>
                        <h2 class="mt-4 text-2xl font-bold">{{ $event->title }}</h2>
                        <p class="mt-3 max-w-3xl text-slate-300">{{ $event->details }}</p>
                    </div>
                    <div class="text-sm text-slate-400">
                        <p>{{ $event->starts_at->format('M d, Y') }}</p>
                        <p>{{ $event->starts_at->format('H:i') }} - {{ $event->ends_at->format('H:i') }}</p>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center justify-between gap-4 text-sm text-slate-400">
                    <span>{{ $event->city }} · {{ $event->venue }}</span>
                    <span>Hosted by {{ $event->host->name }} · {{ $event->attendee_count }}/{{ $event->capacity }} joined</span>
                </div>
            </article>
        @endforeach
    </div>
@endsection
