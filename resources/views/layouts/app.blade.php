<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Khmer Dev Community' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @php($isMobileShell = ($shell ?? 'web') === 'mobile')

        <div class="shell">
            @if ($isMobileShell)
                <div class="mx-auto flex min-h-screen max-w-md flex-col px-4 py-6">
                    <div class="panel relative flex min-h-[calc(100vh-3rem)] flex-col overflow-hidden border-white/12">
                        <div class="border-b border-white/10 px-5 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-orange-300">Mobile</p>
                                    <h1 class="mt-1 text-lg font-bold">{{ $pageTitle ?? 'Khmer Dev' }}</h1>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-xs text-slate-300">
                                    {{ auth()->user()->username ?? 'guest' }}
                                </div>
                            </div>
                        </div>

                        <main class="flex-1 px-4 py-5 pb-24">
                            @yield('content')
                        </main>

                        @include('partials.mobile-nav')
                    </div>
                </div>
            @else
                <div class="mx-auto grid min-h-screen max-w-7xl gap-6 px-4 py-6 lg:grid-cols-[260px_minmax(0,1fr)]">
                    <aside class="panel hidden p-4 lg:block">
                        @include('partials.web-nav')
                    </aside>

                    <div class="space-y-6">
                        <header class="panel flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-orange-300">Khmer Dev Community</p>
                                <h1 class="mt-2 text-2xl font-bold">{{ $pageTitle ?? 'Build in public with Cambodia\'s dev scene' }}</h1>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                @auth
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300">
                                        {{ auth()->user()->name }}
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="rounded-2xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white" type="submit">Logout</button>
                                    </form>
                                @else
                                    <a class="rounded-2xl border border-white/10 px-4 py-2 text-sm text-slate-200" href="{{ route('login') }}">Sign in</a>
                                    <a class="rounded-2xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white" href="{{ route('register') }}">Create account</a>
                                @endauth
                            </div>
                        </header>

                        <main>
                            @yield('content')
                        </main>
                    </div>
                </div>
            @endif
        </div>
    </body>
</html>
