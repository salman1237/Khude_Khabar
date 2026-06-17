<x-guest-layout>
    <h1 class="font-display text-xl font-bold text-slate-800 mb-1">অ্যাকাউন্ট তৈরি করুন 🌱</h1>
    <p class="text-sm text-slate-500 mb-5">আপনার শিশুর পুষ্টিকর খাবারের যাত্রা শুরু করুন।</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="field-label">আপনার নাম</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="field-input" required autofocus autocomplete="name">
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <label for="email" class="field-label">ইমেইল</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="field-input" required autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="field-label">পাসওয়ার্ড</label>
            <input id="password" type="password" name="password" class="field-input" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <label for="password_confirmation" class="field-label">পাসওয়ার্ড নিশ্চিত করুন</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="field-input" required autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="btn-primary w-full">অ্যাকাউন্ট তৈরি করুন</button>

        <p class="text-center text-sm text-slate-500 pt-1">
            ইতিমধ্যে অ্যাকাউন্ট আছে?
            <a class="text-brand-green font-medium hover:underline" href="{{ route('login') }}">লগ ইন করুন</a>
        </p>
    </form>
</x-guest-layout>
