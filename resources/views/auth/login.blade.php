<x-guest-layout>
    <h1 class="font-display text-xl font-bold text-slate-800 mb-1">আবার স্বাগতম 👋</h1>
    <p class="text-sm text-slate-500 mb-5">আপনার অ্যাকাউন্টে লগ ইন করুন।</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="field-label">ইমেইল</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="field-input" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="field-label">পাসওয়ার্ড</label>
            <input id="password" type="password" name="password" class="field-input" required autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-brand-sand text-brand-green focus:ring-brand-green/40" name="remember">
            <span class="ms-2 text-sm text-slate-600">আমাকে মনে রাখুন</span>
        </label>

        <button type="submit" class="btn-primary w-full">লগ ইন</button>

        <div class="flex items-center justify-between text-sm pt-1">
            @if (Route::has('password.request'))
                <a class="text-slate-500 hover:text-brand-green" href="{{ route('password.request') }}">
                    পাসওয়ার্ড ভুলে গেছেন?
                </a>
            @endif
            <a class="text-brand-green font-medium hover:underline" href="{{ route('register') }}">
                নতুন অ্যাকাউন্ট
            </a>
        </div>
    </form>
</x-guest-layout>
