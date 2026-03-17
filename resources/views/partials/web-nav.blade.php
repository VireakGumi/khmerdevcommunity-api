<div class="flex h-full flex-col justify-between gap-6">
    <div class="space-y-6">
        <div class="rounded-3xl bg-white p-5 text-slate-950">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">KDC</p>
            <h2 class="mt-2 text-2xl font-bold">GitHub x Dev.to x Discord</h2>
            <p class="mt-3 text-sm text-slate-600">A product-first community for Khmer developers to write, ship, meet, and collaborate.</p>
        </div>

        <nav class="space-y-2">
            <a class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}" href="{{ route('home') }}">Home <span>01</span></a>
            <a class="nav-link {{ request()->routeIs('feed') ? 'nav-link-active' : '' }}" href="{{ route('feed') }}">Community Feed <span>02</span></a>
            <a class="nav-link {{ request()->routeIs('projects') ? 'nav-link-active' : '' }}" href="{{ route('projects') }}">Projects <span>03</span></a>
            <a class="nav-link {{ request()->routeIs('events') ? 'nav-link-active' : '' }}" href="{{ route('events') }}">Events <span>04</span></a>
            <a class="nav-link {{ request()->routeIs('profiles') ? 'nav-link-active' : '' }}" href="{{ route('profiles') }}">Developer Profiles <span>05</span></a>
        </nav>
    </div>

    <div class="panel-soft p-4">
        <p class="text-xs uppercase tracking-[0.3em] text-orange-300">Mobile Routes</p>
        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
            <a class="rounded-xl bg-white/5 px-3 py-2 text-slate-300" href="{{ route('mobile.feed') }}">Feed</a>
            <a class="rounded-xl bg-white/5 px-3 py-2 text-slate-300" href="{{ route('mobile.post') }}">Post</a>
            <a class="rounded-xl bg-white/5 px-3 py-2 text-slate-300" href="{{ route('mobile.notifications') }}">Alerts</a>
            <a class="rounded-xl bg-white/5 px-3 py-2 text-slate-300" href="{{ route('mobile.messages') }}">Messages</a>
        </div>
    </div>
</div>
