@php
    $navItems = [
        ['route' => 'dashboard', 'label' => 'ড্যাশবোর্ড', 'icon' => '🏠', 'active' => request()->routeIs('dashboard')],
        ['route' => 'children.create', 'label' => 'নতুন শিশু', 'icon' => '➕', 'active' => request()->routeIs('children.create')],
        ['route' => 'profile.edit', 'label' => 'প্রোফাইল', 'icon' => '👤', 'active' => request()->routeIs('profile.edit')],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-brand-sand flex flex-col transform transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="flex items-center justify-between px-5 py-5 border-b border-brand-sand">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="text-3xl">🍚</span>
            <span>
                <span class="block font-display text-lg font-bold text-brand-green leading-tight">{{ config('app.name', 'Khude Khabar') }}</span>
                <span class="block text-[11px] text-slate-400">শিশুর পুষ্টি সঙ্গী</span>
            </span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden p-1 text-slate-400" aria-label="বন্ধ করুন">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
        @foreach ($navItems as $item)
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition',
                    'bg-brand-green text-white shadow-soft' => $item['active'],
                    'text-slate-600 hover:bg-brand-cream' => ! $item['active'],
                ])
            >
                <span class="text-lg">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="px-3 py-4 border-t border-brand-sand">
        <div class="px-3 pb-3 text-sm">
            <p class="font-semibold text-slate-700 truncate">{{ auth()->user()?->name }}</p>
            <p class="text-xs text-slate-400 truncate">{{ auth()->user()?->email }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-rose-600 hover:bg-rose-50 transition">
                <span class="text-lg">🚪</span> লগ আউট
            </button>
        </form>
    </div>
</aside>
