<nav class="absolute inset-x-0 bottom-0 border-t border-white/10 bg-slate-950/95 px-3 py-3 backdrop-blur">
    <div class="grid grid-cols-5 gap-2 text-center text-[11px] text-slate-400">
        <a class="{{ request()->routeIs('mobile.feed') ? 'text-white' : '' }}" href="{{ route('mobile.feed') }}">Feed</a>
        <a class="{{ request()->routeIs('mobile.post') ? 'text-white' : '' }}" href="{{ route('mobile.post') }}">Post</a>
        <a class="{{ request()->routeIs('mobile.notifications') ? 'text-white' : '' }}" href="{{ route('mobile.notifications') }}">Alerts</a>
        <a class="{{ request()->routeIs('mobile.profile') ? 'text-white' : '' }}" href="{{ route('mobile.profile') }}">Profile</a>
        <a class="{{ request()->routeIs('mobile.messages') ? 'text-white' : '' }}" href="{{ route('mobile.messages') }}">Inbox</a>
    </div>
</nav>
