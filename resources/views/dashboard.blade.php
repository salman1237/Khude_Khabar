<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold text-slate-800">
                স্বাগতম, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-sm text-slate-500 mt-0.5">আপনার শিশুদের পুষ্টিকর খাবারের পরিকল্পনা এখানে।</p>
            </div>
            <a href="{{ route('children.create') }}" class="btn-primary hidden sm:inline-flex">
                ➕ নতুন শিশু
            </a>
        </div>
    </x-slot>

    @if ($children->isEmpty())
        @include('partials.empty-children')
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($children as $child)
                <x-child-card :child="$child" />
            @endforeach

            <!-- Add-child tile -->
            <a href="{{ route('children.create') }}"
               class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-brand-sand text-slate-400 hover:text-brand-green hover:border-brand-green/50 transition min-h-[220px]">
                <span class="text-4xl">➕</span>
                <span class="font-medium">নতুন শিশু যোগ করুন</span>
            </a>
        </div>
    @endif
</x-app-layout>
