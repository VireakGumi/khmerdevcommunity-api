@extends('layouts.app', ['title' => 'Login', 'pageTitle' => 'Sign in to your Khmer developer workspace'])

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="panel p-6">
            <form class="space-y-5" method="POST" action="{{ route('login.store') }}">
                @csrf
                <div>
                    <label class="mb-2 block text-sm text-slate-300" for="email">Email</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300" for="password">Password</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="password" name="password" type="password" required>
                </div>
                <label class="flex items-center gap-3 text-sm text-slate-400">
                    <input class="rounded border-white/20 bg-white/5" name="remember" type="checkbox">
                    Keep me signed in
                </label>
                <button class="w-full rounded-2xl bg-orange-500 px-5 py-3 font-semibold text-white" type="submit">Sign in</button>
            </form>
            <p class="mt-4 text-sm text-slate-400">Demo account: `dara@khdev.community` / `password`</p>
        </div>
    </div>
@endsection
