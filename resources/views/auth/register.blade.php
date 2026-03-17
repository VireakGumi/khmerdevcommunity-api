@extends('layouts.app', ['title' => 'Register', 'pageTitle' => 'Create your Khmer Dev Community account'])

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="panel p-6">
            <form class="grid gap-5 md:grid-cols-2" method="POST" action="{{ route('register.store') }}">
                @csrf
                <div>
                    <label class="mb-2 block text-sm text-slate-300" for="name">Full name</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="name" name="name" type="text" value="{{ old('name') }}" required>
                    @error('name') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300" for="username">Username</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="username" name="username" type="text" value="{{ old('username') }}" required>
                    @error('username') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm text-slate-300" for="email">Email</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300" for="password">Password</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="password" name="password" type="password" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm text-slate-300" for="password_confirmation">Confirm password</label>
                    <input class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none" id="password_confirmation" name="password_confirmation" type="password" required>
                </div>
                <div class="md:col-span-2">
                    <button class="w-full rounded-2xl bg-orange-500 px-5 py-3 font-semibold text-white" type="submit">Join the community</button>
                </div>
            </form>
        </div>
    </div>
@endsection
